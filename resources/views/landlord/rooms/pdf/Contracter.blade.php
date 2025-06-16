 <style>
    body {
        font-family: "DejaVu Sans", sans-serif;
    }
</style>

<h2>HỢP ĐỒNG THUÊ PHÒNG</h2>

<p><strong>Chủ trọ:</strong> {{ $landlord->name }}</p>
<p><strong>SĐT:</strong> {{ $landlord->phone_number }}</p>
<p><strong>CCCD:</strong> {{ $landlord->identity_number }}</p>

<p><strong>Số phòng:</strong> {{ $room->room_number }}</p>
<p><strong>Diện tích:</strong> {{ $room->area }} m²</p>
<p><strong>Giá thuê:</strong> {{ number_format($room->rental_price) }} VNĐ</p>

<h4>Tiện nghi:</h4>
<ul>
@foreach ($room->facilities as $fac)
    <li>{{ $fac->name }}</li>
@endforeach
</ul>

<h4>Dịch vụ:</h4>
<ul>
@foreach ($room->services as $service)
    @php
        $unitLabel = '';

        switch ($service->service_id) {
            case 1: // Điện
                $unitLabel = 'số';
                break;

            case 2: // Nước
                $unitLabel = $service->pivot->unit === 'per_m3' ? 'm³' : 'người';
                break;

            case 3: // Internet
                $unitLabel = $service->pivot->unit === 'per_room' ? 'phòng' : 'người';
                break;

            case 4: // Gửi xe máy
            case 5: // Rác thải
            case 6: // Dọn vệ sinh
                $unitLabel = 'phòng';
                break;

            default:
                $unitLabel = $service->pivot->unit ?? '';
        }
    @endphp

    <li>
        {{ $service->name }}:
        {{ $service->pivot->is_free ? 'Miễn phí' : number_format($service->pivot->price) . ' VNĐ/' . $unitLabel }}
    </li>
@endforeach
</ul>


<h4>Thông tin người thuê:</h4>
<ul>
    <li>Họ tên: ....................................</li>
    <li>SĐT: ....................................</li>
    <li>CCCD: ....................................</li>
    <li>Email.......................................</li>
    <li>Số lượng người ở: ....................................</li>

</ul>

<h4>Điều khoản thuê:</h4>
<p>- Thời gian thuê: tối thiểu 6 tháng.</p>
<p>- Đặt cọc 1 tháng tiền nhà.</p>
<p>- Không tự ý chuyển nhượng.</p>
<p>...</p>

<h4>Chữ ký các bên:</h4>
<table style="width: 100%; margin-top: 50px;">
    <tr>
        <td style="width: 50%; text-align: center;">
            <strong>BÊN CHO THUÊ (CHỦ TRỌ)</strong><br><br><br><br>
            (Ký và ghi rõ họ tên)<br>
            ......................................
            {{-- {{ $landlord->name }} --}}
        </td>
        <td style="width: 50%; text-align: center;">
            <strong>BÊN THUÊ (NGƯỜI THUÊ)</strong><br><br><br><br>
            (Ký và ghi rõ họ tên)<br>
            ......................................
        </td>
    </tr>
</table>