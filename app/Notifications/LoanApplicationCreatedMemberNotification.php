<?php

namespace App\Notifications;

use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class LoanApplicationCreatedMemberNotification extends Notification
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
        return (new MailMessage)
            ->subject('Loan Application Received')
            ->line('We have received your loan application.')
            ->action('View application', route('portal.loans.applications.index'))
            ->line('Thank you for using our application!');
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content('We have received your loan application.');
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
