<?php

namespace App\Models\Observers;

//use App\Events\ChatMessageUserEvent;
use App\Models\Chat_Message;


class ChatMessageObserver
{
    /**
     * Hook into Notification creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param Chat_Message $chatMessage
     * @return void
     */
    public function deleted(Chat_Message $chatMessage)
    {
        error_log('ChatMessageObserver----deleted -----');
    }

    public function created(Chat_Message $chatMessage)
    {
        // có gửi thông báo kiểu notification ? => gui len redis
        error_log('ChatMessageObserver----created -----');
    }
    public function updated(Chat_Message $chatMessage)
    {
        //
        error_log('ChatMessageObserver----updated -----');
    }
    public function saved(Chat_Message $chatMessage)
    {
        //
        error_log('ChatMessageObserver----saved -----'.$chatMessage->id);
       // NotificationEvent::dispatch($notification);
        
    }

    public function restored(Chat_Message $chatMessage)
    {
        //
        error_log('ChatMessageObserver----restored -----');
    }
}