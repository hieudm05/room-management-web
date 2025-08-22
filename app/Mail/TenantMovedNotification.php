<?php

namespace App\Mail;

use App\Models\Landlord\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantMovedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $tenantData; 
    public $newRoom;

    public function __construct($tenantData, Room $newRoom)
    {
        $this->tenantData = $tenantData;
        $this->newRoom = $newRoom;
    }

    public function build()
    {
        return $this->subject('📢 Khách thuê đã chuyển sang phòng mới')
            ->view('emails.tenant_moved')
            ->with([
                'tenant' => $this->tenantData,
                'room'   => $this->newRoom,
            ]);
    }
}

