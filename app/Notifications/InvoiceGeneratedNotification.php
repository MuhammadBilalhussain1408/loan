<?php

namespace App\Notifications;

use App\Models\CommunicationTemplate;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use PDF;

class InvoiceGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Invoice $invoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
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
        $invoice = $this->invoice;
        $template = CommunicationTemplate::where('system_name', 'send_invoice_to_patient_email_template')->first();
        $body = template_replace_tags([
            'body'=>$template->description,
            'patient_id'=>$invoice->patient_id,
            'invoice_id'=>$invoice->id,
        ]);
        $email = (new MailMessage)
            ->subject($template->subject)->markdown('emails.basic_email', ['body' => $body]);

        $pdf = PDF::loadView('invoices.print', ['invoice' => $invoice]);
        $email->attachData($pdf->outPut(), ' Invoice#' . $invoice->reference . ".pdf");
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
