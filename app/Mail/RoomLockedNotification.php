<?php

namespace App\Mail;

use App\Models\Landlord\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RoomLockedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $room;
    public $reason;
    public $suggestedRooms;
    public $agreementId; // 👈 thêm biến này

    public function __construct(Room $room, $reason, $suggestedRooms, $agreementId)
    {
        $this->room = $room;
        $this->reason = $reason;
        $this->suggestedRooms = $suggestedRooms;
        $this->agreementId = $agreementId; // gán vào
    }

    public function build()
    {
        return $this->subject('Thông báo về phòng bạn đang thuê')
            ->view('emails.room_locked')
            ->with([
                'room'           => $this->room,
                'reason'         => $this->reason,
                'suggestedRooms' => $this->suggestedRooms,
                'agreementId'    => $this->agreementId, // truyền ra view
            ]);
    }
}
