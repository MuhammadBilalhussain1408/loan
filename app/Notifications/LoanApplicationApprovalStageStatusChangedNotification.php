<?php

namespace App\Notifications;

use App\Models\CommunicationTemplate;
use App\Models\LoanApplicationLinkedApprovalStage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class LoanApplicationApprovalStageStatusChangedNotification extends Notification
{
    use Queueable;

    public LoanApplicationLinkedApprovalStage $stage;

    /**
     * Create a new notification instance.
     */
    public function __construct(LoanApplicationLinkedApprovalStage $stage)
    {
        $this->stage = $stage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }
        if (!empty($notifiable->contact_number)) {
            $channels[] = TwilioChannel::class;
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $template = CommunicationTemplate::where('system_name', 'loan_application_approval_stage_status_changed_member_notification_email')->first();
        if (!empty($template)) {
            $message = template_replace_tags([
                'body' => $template->description,
                'loan_application_id' => $this->stage->loan_application_id,
                'member_id' => $this->stage->application->member_id,
                'loan_application_linked_approval_stage_id' => $this->stage->id,
            ]);
            return (new MailMessage)
                ->subject($template->subject)
                ->line(new HtmlString($message))
                ->action('View application', route('portal.loans.applications.index'))
                ->line('Thank you for using our application!');
        }
        return (new MailMessage)
            ->subject('Loan Application Approval Stage Status Changed')
            ->line('Hello ' . $notifiable->first_name . ', your loan application #' . $this->stage->loan_application_id . ' status for stage ' . $this->stage->stage->name . ' has changed to ' . $this->stage->status . '.')
            ->action('View application', route('portal.loans.applications.index'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function toTwilio($notifiable)
    {
        $template = CommunicationTemplate::where('system_name', 'loan_application_approval_stage_status_changed_member_notification_sms')->first();
        $message = 'Hello ' . $notifiable->first_name . ', your loan application #' . $this->stage->loan_application_id . ' status for stage ' . $this->stage->stage->name . ' has changed to ' . $this->stage->status . '.';
        if (!empty($template)) {
            $message = template_replace_tags([
                'body' => $template->description,
                'loan_application_id' => $this->stage->loan_application_id,
                'member_id' => $this->stage->application->member_id,
                'loan_application_linked_approval_stage_id' => $this->stage->id,
            ]);
        }
        return (new TwilioSmsMessage())
            ->content($message);
    }
}
