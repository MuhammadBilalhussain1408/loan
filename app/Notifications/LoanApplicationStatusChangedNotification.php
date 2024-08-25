<?php

namespace App\Notifications;

use App\Models\CommunicationTemplate;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class LoanApplicationStatusChangedNotification extends Notification
{
    use Queueable;

    public LoanApplication $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(LoanApplication $application)
    {
        $this->application = $application;
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
        $template = CommunicationTemplate::where('system_name', 'loan_application_status_changed_member_notification_email')->first();
        if (!empty($template)) {
            $message = template_replace_tags([
                'body' => $template->description,
                'loan_application_id' => $this->application->id,
                'member_id' => $this->application->member_id,

            ]);
            return (new MailMessage)
                ->subject($template->subject)
                ->line(new HtmlString($message))
                ->action('View application', route('portal.loans.applications.index'))
                ->line('Thank you for using our application!');
        }
        return (new MailMessage)
            ->subject('Loan Application Approval Stage Status Changed')
            ->line('Hello ' . $notifiable->first_name . ', your loan application #' . $this->application->id . ' status has changed to ' . $this->application->status . '.')
            ->action('View application', route('portal.loans.applications.index'))
            ->line('Thank you for using our application!');
    }

    public function toTwilio($notifiable)
    {
        $template = CommunicationTemplate::where('system_name', 'loan_application_status_changed_member_notification_sms')->first();
        $message ='Hello ' . $notifiable->first_name . ', your loan application #' . $this->application->id . ' status has changed to ' . $this->application->status . '.';
        if (!empty($template)) {
            $message = template_replace_tags([
                'body' => $template->description,
                'loan_application_id' => $this->application->id,
                'member_id' => $this->application->member_id,
            ]);
        }
        return (new TwilioSmsMessage())
            ->content($message);
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
}
