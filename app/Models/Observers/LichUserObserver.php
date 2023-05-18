<?php

namespace App\Models\Observers;

use App\Models\LichNguoithamgia;
use App\Services\LichSendNotificationService;

class LichUserObserver
{
    /**
     * Hook into LichNguoithamgia creating, created, updating, updated, saving, saved, deleting, deleted, restoring và restored.
     *
     * @param LichNguoithamgia $lich_user
     * @return void
     */
    public function deleted(LichNguoithamgia $lich_user)
    {
    }

    public function created(LichNguoithamgia $lich_user)
    {
        //error_log('LichUserObserver----created -----');
    }
    public function updated(LichNguoithamgia $lich_user)
    {
        //
        //error_log('LichUserObserver----updated -----');
    }
    public function saved(LichNguoithamgia $lich_user)
    {
        //
        error_log('LichUserObserver----saved -----');
        // gửi thông báo cho người được assign
       
        $lich = $lich_user->lich;
        if (!isset($lich) || isset($lich_user)) {
            return;
        }
        $lstUserId = [];
        $lstDonViId = [];
        $lstUser = isset($lich_user->user_id) && $lich_user->user_id != null ? [$lich_user->user_id] : [];
        if (isset($lich_user->user_id) && $lich_user->user_id) {
            array_push($lstUserId, $lich_user->user_id);
        } else if (isset($lich_user->donvi_id) && $lich_user->donvi_id) {
            array_push($lstUserId, $lich_user->user_id);
        }
        LichSendNotificationService::registerNofitication($lich, $lich_user->status, $lstUserId, $lstDonViId);
    }

    public function restored(LichNguoithamgia $lich_user)
    {
        //
        error_log('LichUserObserver----restored -----');
    }
}