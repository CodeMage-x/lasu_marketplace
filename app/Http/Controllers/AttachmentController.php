<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serve private message attachments through an authenticated controller
 * so they are never directly web-accessible (VULN-21).
 */
class AttachmentController extends Controller
{
    public function download(Request $request, Message $message): StreamedResponse
    {
        $user         = $request->user();
        $conversation = $message->conversation;

        // Only the two participants of the conversation may download attachments
        abort_unless(
            $conversation->buyer_id === $user->id || $conversation->seller_id === $user->id,
            403
        );

        abort_unless($message->attachment_path, 404);

        abort_unless(
            Storage::disk('local')->exists($message->attachment_path),
            404
        );

        return Storage::disk('local')->download($message->attachment_path);
    }
}
