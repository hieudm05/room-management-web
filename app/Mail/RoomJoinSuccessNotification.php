<?php

namespace App\Mail;

use App\Models\Landlord\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RoomJoinSuccessNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $room;

    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    public function build()
    {
        return $this->subject('✅ Bạn đã tham gia phòng mới thành công')
            ->view('emails.room_join_success')
            ->with([
                'room' => $this->room,
            ]);
    }
}
