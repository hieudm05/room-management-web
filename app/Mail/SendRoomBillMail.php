<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendRoomBillMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $bankAccount;

    public function __construct($data, $bankAccount)
    {
        $this->data = $data;
        $this->bankAccount = $bankAccount;
    }

    public function build()
    {
        return $this->subject('Hóa đơn phòng trọ tháng ' . $this->data['month'])
            ->markdown('emails.room_bill')
            ->with([
                'data' => $this->data,
                'bankAccount' => $this->bankAccount,
            ]);
    }
}