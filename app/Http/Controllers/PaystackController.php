<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\PaymentConfirmedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackController extends Controller
{
    private string $secretKey;
    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
    }

    /**
     * Initiate a Paystack payment for a given order.
     */
    public function initiate(Order $order): RedirectResponse
    {
        abort_unless($order->buyer_id === auth()->id(), 403);
        abort_unless($order->payment_status === 'unpaid', 422, 'This order has already been paid.');

        $user = auth()->user();

        // Use a cryptographically random reference — not predictable from order ID or time
        $reference = 'LASU-' . strtoupper(Str::random(24));

        $response = $this->paystackPost('/transaction/initialize', [
            'email'        => $user->email,
            'amount'       => (int) ($order->total_amount * 100),
            'reference'    => $reference,
            'callback_url' => route('payment.callback'),
            'metadata'     => [
                'order_id'    => $order->id,
                'order_number'=> $order->order_number,
                'buyer_name'  => $user->name,
            ],
        ]);

        if (!$response || !($response['status'] ?? false)) {
            Log::error('Paystack initiate failed', ['order_id' => $order->id, 'response' => $response]);
            return back()->with('error', 'Could not initiate payment. Please try again.');
        }

        $order->payment()->updateOrCreate(
            ['order_id' => $order->id],
            ['provider_reference' => $response['data']['reference'], 'status' => 'pending']
        );

        Log::info('Payment initiated', ['order_id' => $order->id, 'reference' => $reference, 'user_id' => $user->id]);

        return redirect($response['data']['authorization_url']);
    }

    /**
     * Paystack redirects user here after payment.
     */
    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (!$reference || !preg_match('/^LASU-[A-Z0-9]{24}$/', $reference)) {
            Log::warning('Payment callback with invalid reference', ['reference' => $reference, 'ip' => $request->ip()]);
            return redirect()->route('buyer.orders.index')->with('error', 'Invalid payment reference.');
        }

        $response = $this->paystackGet('/transaction/verify/' . rawurlencode($reference));

        if (!$response || ($response['data']['status'] ?? '') !== 'success') {
            Log::warning('Payment verification failed', ['reference' => $reference]);
            return redirect()->route('buyer.orders.index')->with('error', 'Payment verification failed.');
        }

        $this->fulfillPayment($response['data']);

        return redirect()->route('buyer.orders.index')->with('success', 'Payment successful! Your order is confirmed.');
    }

    /**
     * Paystack webhook for server-side confirmation.
     */
    public function webhook(Request $request): Response
    {
        $signature = $request->header('x-paystack-signature');
        $hash      = hash_hmac('sha512', $request->getContent(), $this->secretKey);

        if (!$signature || !hash_equals($hash, $signature)) {
            Log::warning('Invalid Paystack webhook signature', ['ip' => $request->ip()]);
            return response('Unauthorized', 401);
        }

        $payload = $request->json()->all();

        if (($payload['event'] ?? '') === 'charge.success') {
            $this->fulfillPayment($payload['data']);
        }

        return response('OK', 200);
    }

    /**
     * Shared logic to mark order paid after successful Paystack response.
     * Wrapped in a DB transaction with a row lock to prevent race conditions.
     */
    private function fulfillPayment(array $data): void
    {
        $orderId = $data['metadata']['order_id'] ?? null;
        if (!$orderId) {
            Log::error('fulfillPayment called without order_id in metadata', ['data' => $data]);
            return;
        }

        DB::transaction(function () use ($data, $orderId) {
            // Lock the order row to prevent concurrent double-fulfillment (VULN-03)
            $order = Order::lockForUpdate()->find($orderId);

            if (!$order) {
                Log::error('fulfillPayment: order not found', ['order_id' => $orderId]);
                return;
            }

            if ($order->payment_status === 'paid') {
                return;
            }

            // Verify the amount paid matches the order total (VULN-02)
            $paidAmount = ($data['amount'] ?? 0) / 100;
            if (abs($paidAmount - (float) $order->total_amount) > 0.01) {
                Log::critical('Payment amount mismatch — possible fraud', [
                    'order_id'     => $order->id,
                    'order_total'  => $order->total_amount,
                    'paid_amount'  => $paidAmount,
                    'reference'    => $data['reference'] ?? null,
                ]);
                return;
            }

            $payment = $order->payment;
            if (!$payment) {
                Log::error('fulfillPayment: no payment record for order', ['order_id' => $orderId]);
                return;
            }

            $payment->update([
                'status'             => 'success',
                'provider_reference' => $data['reference'],
                'amount'             => $paidAmount,
                'gateway_payload'    => $data,
                'paid_at'            => now(),
            ]);

            $order->update([
                'payment_status' => 'paid',
                'order_status'   => 'confirmed',
                'paid_at'        => now(),
                'confirmed_at'   => now(),
            ]);

            Log::info('Payment fulfilled', [
                'order_id'  => $order->id,
                'amount'    => $paidAmount,
                'reference' => $data['reference'] ?? null,
            ]);

            $order->buyer->notify(new PaymentConfirmedNotification($order));
            $order->seller->notify(new PaymentConfirmedNotification($order));
        });
    }

    /**
     * POST request to Paystack API via Laravel HTTP client (proper TLS, no raw cURL). (VULN-05)
     */
    private function paystackPost(string $endpoint, array $data): ?array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->acceptJson()
                ->post($this->baseUrl . $endpoint, $data);

            if ($response->failed()) {
                Log::error('Paystack POST failed', ['endpoint' => $endpoint, 'status' => $response->status()]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Paystack POST error: ' . $e->getMessage(), ['endpoint' => $endpoint]);
            return null;
        }
    }

    /**
     * GET request to Paystack API via Laravel HTTP client (proper TLS, no raw cURL). (VULN-05)
     */
    private function paystackGet(string $endpoint): ?array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->acceptJson()
                ->get($this->baseUrl . $endpoint);

            if ($response->failed()) {
                Log::error('Paystack GET failed', ['endpoint' => $endpoint, 'status' => $response->status()]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Paystack GET error: ' . $e->getMessage(), ['endpoint' => $endpoint]);
            return null;
        }
    }
}
