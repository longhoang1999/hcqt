<?php

namespace App\Models\Observers;
use App\Services\LichSendNotificationService;
use App\Models\Lich;
class LichObserver
{
    /**
     * Hook into lich creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param Lich $lich
     * @return void
     */
    public function deleted(Lich $lich)
    {
        /* $lich->diadiem()->delete();
        $lich->danhsach_nguoithamgia()->delete();
        $lich->danhsach_donvithamgia()->delete();
        $lich->lichduyet()->delete();
        $lich->comments()->delete();
        $lich->attachedFiles()->delete();
        $lich->bienBanFiles()->delete(); */
    }

    public function created(Lich $lich)
    {
        //
        //error_log('LichObserver----created -----');
    }
    public function updated(Lich $lich)
    {
        //
        //error_log('LichObserver----updated -----');
    }
    public function saved(Lich $lich)
    {
        LichSendNotificationService::sendNofitication($lich);
        
        
        
    }

    public function restored(Lich $lic)
    {
        //
        error_log('LichObserver----restored -----');
    }
}