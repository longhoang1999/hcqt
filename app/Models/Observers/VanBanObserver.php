<?php

namespace App\Models\Observers;

use App\Models\VanBan;
use App\Services\VanBanSendNotificationService;

class VanBanObserver
{
    /**
     * Hook into lich creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param VanBan $vanBan
     * @return void
     */
    public function deleted(VanBan $vanBan)
    {
    }

    public function created(VanBan $vanBan)
    {
    }
    public function updated(VanBan $vanBan)
    {
    }
    public function saved(VanBan $vanBan)
    {
        VanBanSendNotificationService::sendNofitication($vanBan);
        
    }

    public function restored(VanBan $vanBan)
    {
       
    }
}