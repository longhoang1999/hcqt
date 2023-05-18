<?php

namespace App\Models\Observers;

use App\Models\VanBan_ButPhe;
use App\Services\VanBanSendNotificationService;

class VanBanButPheObserver
{
    /**
     * Hook into lich creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param VanBan_ButPhe $vanBan_ButPhe
     * @return void
     */
    public function deleted(VanBan_ButPhe $vanBan_ButPhe)
    {
    }

    public function created(VanBan_ButPhe $vanBan_ButPhe)
    {
        VanBanSendNotificationService::sendNofiticationToButPhe($vanBan_ButPhe);
    }
    public function updated(VanBan_ButPhe $vanBan_ButPhe)
    {
    }
    public function saved(VanBan_ButPhe $vanBan_ButPhe)
    {
       
        
    }

    public function restored(VanBan_ButPhe $vanBan_ButPhe)
    {
       
    }
}