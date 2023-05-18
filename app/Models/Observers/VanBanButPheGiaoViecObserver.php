<?php

namespace App\Models\Observers;

use App\Models\VanBan_ButPhe_GiaoViec;
use App\Services\VanBanSendNotificationService;

class VanBanButPheGiaoViecObserver
{
    /**
     * Hook into lich creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param VanBan_ButPhe_GiaoViec $vanBan_ButPhe_GiaoViec
     * @return void
     */
    public function deleted(VanBan_ButPhe_GiaoViec $vanBan_ButPhe_GiaoViec)
    {
    }

    public function created(VanBan_ButPhe_GiaoViec $vanBan_ButPhe_GiaoViec)
    {
        VanBanSendNotificationService::sendNofiticationToButPheGiaoViec($vanBan_ButPhe_GiaoViec);
    }
    public function updated(VanBan_ButPhe_GiaoViec $vanBan_ButPhe_GiaoViec)
    {
    }
    public function saved(VanBan_ButPhe_GiaoViec $vanBan_ButPhe_GiaoViec)
    {
       
        
    }

    public function restored(VanBan_ButPhe_GiaoViec $vanBan_ButPhe_GiaoViec)
    {
       
    }
}