<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public Conversation $conversation, public Message $message) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'new_message',
            'title'           => 'New Message',
            'body'            => "{$this->message->sender->name} sent you a message about \"{$this->conversation->listing->title}\".",
            'conversation_id' => $this->conversation->id,
            'url'             => route('conversations.show', $this->conversation->id),
        ];
    }
}
