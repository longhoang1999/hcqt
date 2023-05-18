<?php

namespace App\Models\Observers;

use App\Models\VanBan_XinYKien;
use App\Services\VanBanSendNotificationService;

class VanBanXinYKienObserver
{
    /**
     * Hook into lich creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param VanBan_XinYKien $vanBan_XinYKien
     * @return void
     */
    public function deleted(VanBan_XinYKien $vanBan_XinYKien)
    {
    }

    public function created(VanBan_XinYKien $vanBan_XinYKien)
    {
        VanBanSendNotificationService::sendNofiticationToXinYKien($vanBan_XinYKien);
    }
    public function updated(VanBan_XinYKien $vanBan_XinYKien)
    {
    }
    public function saved(VanBan_XinYKien $vanBan_XinYKien)
    {
       
        
    }

    public function restored(VanBan_XinYKien $vanBan_XinYKien)
    {
       
    }
}