<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\MpesaTransactionStatus;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendMpesaNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $message;
    protected $transaction_status;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $message, $transaction_status)
    {
        $this->user = $user;
        $this->message = $message;
        $this->transaction_status = $transaction_status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->notify(new MpesaTransactionStatus($this->message, $this->transaction_status));
    }
}
