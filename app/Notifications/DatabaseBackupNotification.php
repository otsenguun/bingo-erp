<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DatabaseBackupNotification extends Notification
{
    use Queueable;

    public $backupFilePath;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($backupFilePath)
    {
        $this->backupFilePath = $backupFilePath;
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
            ->subject('Daily Database Backup Completed')
            ->line('The daily database backup has been completed successfully.')
            ->line('Date: ' . Carbon::now()->format('Y-m-d H:i:s'))
            ->attach($this->backupFilePath, [
                'as' => 'database-backup-' . now()->format('Y-m-d') . '.sql',
                'mime' => 'application/sql',
            ])
            ->line('The backup file is attached to this email.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
