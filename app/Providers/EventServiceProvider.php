<?php

namespace App\Providers;


use App\Events\CommunicationCampaignSent;

use App\Events\LoanApplicationApprovalStageAssigned;
use App\Events\LoanApplicationApprovalStageNowCurrent;
use App\Events\LoanApplicationApprovalStageStatusChanged;
use App\Events\LoanApplicationCreated;
use App\Events\LoanApplicationCurrentApprovalStageChanged;
use App\Events\LoanApplicationStatusChanged;
use App\Events\LoanCreated;
use App\Events\LoanStatusChanged;
use App\Events\LoanTransactionCreated;


use App\Events\TransactionUpdated;
use App\Listeners\LoanApplicationApprovalStageStatusChangedActions;
use App\Listeners\LoanApplicationCreatedInitialActions;
use App\Listeners\LoanApplicationStatusChangedActions;
use App\Listeners\LoanStatusChangedActions;
use App\Listeners\LoanStatusChangedCampaigns;

use App\Listeners\SendInvoicePaymentCreatedNotifications;

use App\Listeners\SendLoanApplicationApprovalStageAssignedNotifications;
use App\Listeners\SendLoanApplicationApprovalStageNowCurrentNotifications;
use App\Listeners\SendLoanApplicationApprovalStageStatusChangedNotifications;
use App\Listeners\SendLoanApplicationCreatedNotifications;
use App\Listeners\SendLoanCreatedNotifications;
use App\Listeners\SendLoanTransactionCreatedNotifications;
use App\Listeners\UpdateTransactions;
use App\Listeners\UpdateUserLastLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            UpdateUserLastLogin::class,
        ],
        LoanCreated::class => [
            SendLoanCreatedNotifications::class,
        ],
        LoanStatusChanged::class => [
            LoanStatusChangedCampaigns::class,
            LoanStatusChangedActions::class
        ],
        TransactionUpdated::class => [
            UpdateTransactions::class,
        ],
        LoanTransactionCreated::class => [
            SendLoanTransactionCreatedNotifications::class,
        ],
        CommunicationCampaignSent::class => [

        ],
        LoanApplicationCreated::class => [
            SendLoanApplicationCreatedNotifications::class,
            LoanApplicationCreatedInitialActions::class,
        ],

        LoanApplicationStatusChanged::class => [
            LoanApplicationStatusChangedActions::class,
        ],
        LoanApplicationApprovalStageAssigned::class => [
            SendLoanApplicationApprovalStageAssignedNotifications::class,
        ],
        LoanApplicationApprovalStageStatusChanged::class => [
            LoanApplicationApprovalStageStatusChangedActions::class,
            SendLoanApplicationApprovalStageStatusChangedNotifications::class,
        ],
        LoanApplicationApprovalStageNowCurrent::class => [
            SendLoanApplicationApprovalStageNowCurrentNotifications::class,
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
