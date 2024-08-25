<?php

namespace App\Events;

use App\Models\LoanApplicationLinkedApprovalStage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoanApplicationApprovalStageAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LoanApplicationLinkedApprovalStage $linkedApprovalStage;

    /**
     * Create a new event instance.
     */
    public function __construct(LoanApplicationLinkedApprovalStage $linkedApprovalStage)
    {
        $this->linkedApprovalStage = $linkedApprovalStage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
