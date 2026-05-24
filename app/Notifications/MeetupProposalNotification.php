<?php

namespace App\Notifications;

use App\Models\MeetupProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MeetupProposalNotification extends Notification
{
    use Queueable;

    public function __construct(public MeetupProposal $proposal, public string $action) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toArray(object $notifiable): array
    {
        $titles = [
            'new'      => 'New Meetup Proposal',
            'accepted' => 'Meetup Proposal Accepted',
            'declined' => 'Meetup Proposal Declined',
            'counter'  => 'Counter Meetup Proposal',
        ];

        $bodies = [
            'new'      => "{$this->proposal->proposedBy->name} proposed a meetup at {$this->proposal->campusZone->name}.",
            'accepted' => "Your meetup proposal at {$this->proposal->campusZone->name} was accepted!",
            'declined' => "Your meetup proposal at {$this->proposal->campusZone->name} was declined.",
            'counter'  => "{$this->proposal->proposedBy->name} sent a counter-proposal for the meetup.",
        ];

        return [
            'type'        => 'meetup_' . $this->action,
            'title'       => $titles[$this->action] ?? 'Meetup Update',
            'body'        => $bodies[$this->action] ?? '',
            'proposal_id' => $this->proposal->id,
            'url'         => route('conversations.show', $this->proposal->conversation_id),
        ];
    }
}
