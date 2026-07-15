<?php

namespace App\Services;

use App\Enums\FeedbackStatus;
use App\Models\FeedbackReply;
use App\Models\FeedbackTicket;
use App\Models\User;

class FeedbackService
{
    public function createTicket(array $data, User $submitter): FeedbackTicket
    {
        $ticket = new FeedbackTicket($data);
        $ticket->user_id = $submitter->id;
        $ticket->status = FeedbackStatus::Open;
        $ticket->save();

        return $ticket;
    }

    public function reply(FeedbackTicket $ticket, array $data, User $replier): FeedbackReply
    {
        $reply = new FeedbackReply($data);
        $reply->feedback_ticket_id = $ticket->id;
        $reply->user_id = $replier->id;
        $reply->save();

        return $reply;
    }

    public function close(FeedbackTicket $ticket): FeedbackTicket
    {
        $ticket->status = FeedbackStatus::Closed;
        $ticket->closed_at = now();
        $ticket->save();

        return $ticket;
    }
}
