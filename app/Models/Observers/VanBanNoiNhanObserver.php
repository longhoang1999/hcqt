<?php

namespace App\Models\Observers;

use App\Models\VanBan_NoiNhan;
use App\Services\VanBanSendNotificationService;

class VanBanNoiNhanObserver
{
    /**
     * Hook into lich creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param VanBan_NoiNhan $vanBan_NoiNhan
     * @return void
     */
    public function deleted(VanBan_NoiNhan $vanBan_NoiNhan)
    {
    }

    public function created(VanBan_NoiNhan $vanBan_NoiNhan)
    {
        VanBanSendNotificationService::sendNofiticationToNoiNhan($vanBan_NoiNhan);
    }
    public function updated(VanBan_NoiNhan $vanBan_NoiNhan)
    {
    }
    public function saved(VanBan_NoiNhan $vanBan_NoiNhan)
    {
        
    }

    public function restored(VanBan_NoiNhan $vanBan_NoiNhan)
    {
       
    }
}