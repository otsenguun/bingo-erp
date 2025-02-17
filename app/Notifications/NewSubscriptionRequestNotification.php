<?php

namespace App\Notifications;

use App\Models\SubscriptionRequest;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSubscriptionRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Tenant $tenant;
    protected SubscriptionRequest $subscriptionRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SubscriptionRequest $subscriptionRequest, Tenant $tenant)
    {
        $this->subscriptionRequest = $subscriptionRequest;
        $this->tenant = $tenant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New subscription Request from '.config('app.name'))
            ->greeting($this->tenant->name.' has requested for subscription!')
            ->action('View all subscription requests', route('subscription-requests.index'))
            ->line('Thank you!');
    }
}
