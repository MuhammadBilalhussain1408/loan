<?php

namespace App\Notifications;

use App\Models\InvoicePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaymentReceivedAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice_payment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(InvoicePayment $invoice_payment)
    {
        $this->invoice_payment = $invoice_payment;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $invoice_payment = $this->invoice_payment;
        $invoice = $invoice_payment->invoice;
        $email = (new MailMessage)->markdown('emails.invoice_payment_received_admin_notification', compact('invoice_payment', 'invoice'))
            ->subject('Payment Received');
        return $email;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
