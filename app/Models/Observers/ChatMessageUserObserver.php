<?php

namespace App\Models\Observers;

use App\Events\ChatMessageUserEvent;
use App\Models\Chat_Message_User;


class ChatMessageUserObserver
{
    /**
     * Hook into Notification creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param Chat_Message $chatMessage
     * @return void
     */
    public function deleted(Chat_Message_User $chatMessageUser)
    {
        error_log('ChatMessageUserObserver----deleted -----');
        $chatMessageUser->message = null;
        broadcast(new ChatMessageUserEvent($chatMessageUser));
    }

    public function created(Chat_Message_User $chatMessageUser)
    {
        // có gửi thông báo kiểu notification ? => gui len redis
        error_log('ChatMessageObserver----created -----');
    }
    public function updated(Chat_Message_User $chatMessageUser)
    {
        //
        error_log('ChatMessageObserver----updated -----');
    }
    public function saved(Chat_Message_User $chatMessageUser)
    {
        //
        error_log('ChatMessageObserver----saved -----'.$chatMessageUser->id);
       // ChatMessageUserEvent::dispatch($notification);
        
    }

    public function restored(Chat_Message_User $chatMessageUser)
    {
        //
        error_log('ChatMessageObserver----restored -----');
    }
}