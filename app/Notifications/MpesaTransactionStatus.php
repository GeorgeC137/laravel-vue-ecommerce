<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MpesaTransactionStatus extends Notification
{
    use Queueable;

    protected $message;
    protected $transaction_status;

    /**
     * Create a new notification instance.
     */
    public function __construct($message, $transaction_status)
    {
        $this->message = $message;
        $this->transaction_status = $transaction_status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Mpesa Transaction Status')
                    ->greeting('Hello!')
                    ->line($this->message)
                    ->line('Transaction Status: ' . ucfirst($this->transaction_status))
                    ->line('Thank you for using our service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'transaction_status' => $this->transaction_status,
        ];
    }
}
