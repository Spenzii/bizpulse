<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobAssigned extends Notification
{
    use Queueable;

    public $wip;

    /**
     * Create a new notification instance.
     */
    public function __construct($wip)
    {
        $this->wip = $wip;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_assigned',
            'wip_id' => $this->wip->id,
            'wip_name' => $this->wip->name,
            'client_name' => $this->wip->client->name,
            'message' => "You've been assigned to: {$this->wip->name} for {$this->wip->client->name}",
        ];
    }
}
