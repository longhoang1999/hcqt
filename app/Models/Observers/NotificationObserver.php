<?php

namespace App\Models\Observers;

use App\Common\Constant\NotificationConstants;
use App\Events\NotificationEvent;
use App\Models\Notification;

use App\Services\NotificationService;

class NotificationObserver
{
    /**
     * Hook into Notification creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param Notification $notification
     * @return void
     */
    public function deleted(Notification $notification)
    {
        error_log('NotificationObserver----deleted -----');
    }

    public function created(Notification $notification)
    {
        // có gửi thông báo kiểu notification ? => gui len redis
        error_log('NotificationObserver----created -----');
        $notification->is_update = false;
        broadcast(new NotificationEvent($notification));
    }
    public function updated(Notification $notification)
    {
        //
        error_log('NotificationObserver----updated -----');
        $notification->is_update = true;
        broadcast(new NotificationEvent($notification));
    }
    public function saved(Notification $notification)
    {
        //
        error_log('NotificationObserver----saved -----'.$notification->id);
        
        // NotificationEvent::dispatch($notification);
        
    }

    public function restored(Notification $notification)
    {
        //
        error_log('NotificationObserver----restored -----');
    }
}