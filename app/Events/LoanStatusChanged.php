<?php

namespace App\Events;

use App\Models\Loan;
use Illuminate\Queue\SerializesModels;


class LoanStatusChanged
{
    use SerializesModels;

    public Loan $loan;
    public string $oldStatus;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Loan $loan, string $oldStatus = '')
    {
        $this->loan = $loan;
        $this->oldStatus=$oldStatus;
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
