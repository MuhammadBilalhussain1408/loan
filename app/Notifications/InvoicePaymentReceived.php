<?php

namespace App\Notifications;

use App\Models\CommunicationTemplate;
use App\Models\InvoicePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use PDF;

class InvoicePaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoicePayment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(InvoicePayment $invoicePayment)
    {
        $this->invoicePayment = $invoicePayment;
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
        $invoicePayment = $this->invoicePayment;
        $template = CommunicationTemplate::where('system_name', 'invoice_payment_confirmation_email_template')->first();
        $body = template_replace_tags([
            'body' => $template->description,
            'patient_id' => $invoicePayment->patient_id,
            'invoice_id' => $invoicePayment->invoice_id,
            'invoice_payment_id' => $invoicePayment->id,
        ]);
        $email = (new MailMessage)
            ->subject($template->subject)->markdown('emails.basic_email', ['body' => $body]);
        $pdf = PDF::loadView('invoice_payments.print', ['invoicePayment' => $invoicePayment]);
        $email->attachData($pdf->outPut(), ' Receipt#' . $invoicePayment->id . ".pdf");
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
