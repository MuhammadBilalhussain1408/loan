<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use PDF;

class InvoiceReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice;
    public $tenant_url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, $tenant_url = null)
    {
        $this->invoice = $invoice;
        $this->tenant_url = $tenant_url;
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
        $email = (new MailMessage)
            ->subject('Invoice Reminder')->markdown('emails.invoice_reminder', compact('invoice'));

        $pdf = PDF::loadView('invoice.print', compact('invoice'));
        $email->attachData($pdf->outPut(), trans_choice('general.invoice', 1) . ' #' . $invoice->reference . ".pdf");
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
