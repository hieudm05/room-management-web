<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room;
use App\Models\RentalAgreement;
use Illuminate\Support\Facades\Mail;
use App\Mail\RoomJoinSuccessNotification;
use App\Mail\TenantMovedNotification;

class RoomJoinController extends Controller
{
    public function join(Room $room, $agreementId = null)
    {
        // Lấy hợp đồng terminated mới nhất nếu không có agreementId
        if (!$agreementId) {
            $latestTerminated = $room->rentalAgreements()
                ->where('status', RentalAgreement::STATUS_TERMINATED)
                ->latest('updated_at')
                ->first();

            if (!$latestTerminated) {
                return redirect()->back()->with('error', 'Không tìm thấy hợp đồng đã kết thúc để tham gia.');
            }

            $agreementId = $latestTerminated->rental_id;
        }

        $oldAgreement = RentalAgreement::find($agreementId);
        if (!$oldAgreement) {
            return redirect()->back()->with('error', 'Hợp đồng cũ không tồn tại.');
        }

        // Lấy thông tin tenant từ hợp đồng cũ
        $renterId = $oldAgreement->renter_id;
        $fullName = $oldAgreement->full_name;
        $email    = $oldAgreement->email;
        $phone    = $oldAgreement->phone;
        $cccd     = $oldAgreement->cccd;

        // dd($renterId, $fullName, $email, $phone, $cccd);
        // Load property nếu chưa

        // Tạo hợp đồng mới dựa trên phòng mới nhưng giữ thông tin tenant
        $newAgreement = $room->rentalAgreements()->create([
            'renter_id'    => $renterId,
            'landlord_id'  => $room->property->landlord_id,
            'start_date'   => now(),
            'end_date'     => now()->addMonths(12),
            'status'       => RentalAgreement::STATUS_ACTIVE,
            'rental_price' => $room->rental_price,
            'deposit'      => $room->deposit_price,
            'is_active'    => 1,
            'created_by'   => auth()->id() ?? $renterId,
            'full_name'    => $fullName,
            'email'        => $email,
            'phone'        => $phone,
            'cccd'         => $cccd,
        ]);

    // dd($newAgreement->toArray());
    // die;

        // Cập nhật phòng để hiển thị tenant ngay
        $room->update([
            'id_rental_agreements' => $newAgreement->rental_id,
            'people_renter'        => 1,
            'is_contract_locked'   => false,
        ]);

        $room->load('property');

        // Gửi mail tenant
        if ($email) {
            Mail::to($email)->send(new RoomJoinSuccessNotification($room, $fullName, $phone, $cccd));
        }

        // Gửi mail landlord
        Mail::to($room->property->landlord->email)
            ->send(new TenantMovedNotification([
                'full_name' => $fullName,
                'email'     => $email,
                'phone'     => $phone,
                'cccd'      => $cccd,
            ], $room));

        return redirect()->route('renter')->with('success', 'Bạn đã tham gia phòng mới thành công!');
    }
}
