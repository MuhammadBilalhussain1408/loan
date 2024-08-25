<?php

namespace App\Events;

use App\Models\Loan;
use Illuminate\Queue\SerializesModels;


class TransactionUpdated
{
    use SerializesModels;
    public Loan $loan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
