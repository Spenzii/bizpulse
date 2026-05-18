<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BlockerRaised extends Notification
{
    use Queueable;

    protected $wip;
    protected $blocker;

    public function __construct($wip, $blocker)
    {
        $this->wip = $wip;
        $this->blocker = $blocker;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Send email for High or Urgent priority
        if (in_array($this->blocker->priority, ['High', 'Urgent'])) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $priorityLabel = strtoupper($this->blocker->priority);
        
        return (new MailMessage)
            ->subject("{$priorityLabel}: Blocker Raised on {$this->wip->name}")
            ->greeting("Hello " . $notifiable->name . ",")
            ->line("A new **{$this->blocker->priority}** priority blocker has been raised on the job: **{$this->wip->name}**.")
            ->line("Client: **{$this->wip->client->name}**")
            ->line("Description of Impediment: \"{$this->blocker->description}\"")
            ->action('View Job Status', route('wips.show', $this->wip->id))
            ->line('Please review this bottleneck to ensure minimal disruption to the workflow.')
            ->salutation('Best regards, BizPulse Automation');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'wip_id' => $this->wip->id,
            'wip_name' => $this->wip->name,
            'blocker_id' => $this->blocker->id,
            'priority' => $this->blocker->priority,
            'blocker_description' => $this->blocker->description,
            'message' => "A new {$this->blocker->priority} blocker has been raised on " . $this->wip->name,
        ];
    }
}
