<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RoomEditRequestResultNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $note;

    public function __construct($status, $note = null)
    {
        $this->status = $status;
        $this->note = $note;
    }

    public function via($notifiable)
    {
        return ['database']; // sử dụng bảng notifications
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Yêu cầu chỉnh sửa phòng đã được ' . ($this->status === 'approved' ? 'duyệt' : 'từ chối'),
            'status' => $this->status,
            'note' => $this->note,
        ];
    }
}

