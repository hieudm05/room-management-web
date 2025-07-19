<style>
    body {
        font-family: "DejaVu Sans", sans-serif;
        line-height: 1.6;
    }

    .center {
        text-align: center;
    }
</style>

<div class="center">
    <p><strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong></p>
    <p><strong>Độc lập – Tự do – Hạnh phúc</strong></p>
    <h2><strong>HỢP ĐỒNG THUÊ PHÒNG TRỌ</strong></h2>
</div>

<p><strong>Hôm nay, ngày .......... tháng .......... năm 20......., tại căn nhà số ...............</strong></p>

<p><strong>Căn cứ pháp lý:</strong></p>
<ul>
    <li>Bộ luật Dân sự năm 2015;</li>
    <li>Luật Nhà ở năm 2014;</li>
    <li>Các quy định pháp luật hiện hành về cho thuê nhà ở và phòng trọ;</li>
</ul>

<p><strong>Chúng tôi gồm có:</strong></p>

<h4>I. BÊN CHO THUÊ PHÒNG TRỌ (Bên A):</h4>
<ul>
    <li>Họ tên: {{ $landlord->name }}</li>
    <li>SĐT: {{ $landlord->phone_number }}</li>
    <li>CCCD: {{ $landlord->identity_number }}</li>
    <li>Thường trú: ....................................................</li>
</ul>

<h4>II. BÊN THUÊ PHÒNG TRỌ (Bên B):</h4>
<ul>
    <li>Họ tên: ......................................</li>
    <li>SĐT: ......................................</li>
    <li>CCCD: ......................................</li>
    <li>Email: ......................................</li>
    <li>Số lượng người ở: ......................................</li>
    <li>Số lượng người tối đa: {{ $room->occupants ?? '......................................' }}</li>
</ul>

<h4>III. Nội dung hợp đồng:</h4>
<ul>
    <li>Phòng thuê số: {{ $room->room_number }}</li>
    <li>Diện tích: {{ $room->area }} m²</li>
    <li>Giá thuê: {{ number_format($room->rental_price) }} VNĐ / tháng</li>
    <li>Tiền cọc: {{ number_format($deposit_price) }} VNĐ</li>
</ul>

<h4>IV. Tiện nghi đi kèm:</h4>
<ul>
    @foreach ($room->facilities as $fac)
        <li>{{ $fac->name }}</li>
    @endforeach
</ul>

<h4>V. Dịch vụ:</h4>
<ul>
    @foreach ($room->services as $service)
        @php
            $unitLabel = match ($service->service_id) {
                1 => 'số',
                2 => $service->pivot->unit === 'per_m3' ? 'm³' : 'người',
                3, 4 => $service->pivot->unit === 'per_room' ? 'phòng' : 'người',
                5, 6, 7 => 'phòng',
                default => $service->pivot->unit ?? '',
            };
        @endphp
        <li>
            {{ $service->name }}:
            {{ $service->pivot->is_free ? 'Miễn phí' : number_format($service->pivot->price) . ' VNĐ/' . $unitLabel }}
        </li>
    @endforeach
</ul>

<h4>VI. Thời gian hợp đồng:</h4>
<ul>
    <li>Ngày bắt đầu: ........../........../............</li>
    <li>Ngày kết thúc: ........../........../............</li>
</ul>

<h4>VII. Điều khoản thuê:</h4>
<ul>
    <li>Thời gian thuê: tối thiểu 6 tháng.</li>
    <li>Đặt cọc 1 tháng tiền nhà.</li>
    <li>Không tự ý chuyển nhượng hoặc cho người khác thuê lại.</li>
</ul>

<h4>VIII. Nội quy khu trọ:</h4>
@foreach (explode("\n", strip_tags($rules)) as $line)
    @if (trim($line) !== '')
        <p style="margin: 2px 0;">{{ $line }}</p>
    @endif
@endforeach



<h4>CHỮ KÝ CÁC BÊN:</h4>
<table style="width: 100%; margin-top: 50px;">
    <tr>
        <td style="width: 50%; text-align: center;">
            <strong>BÊN A (CHỦ TRỌ)</strong><br><br><br><br>
            (Ký và ghi rõ họ tên)<br>
            ......................................
        </td>
        <td style="width: 50%; text-align: center;">
            <strong>BÊN B (NGƯỜI THUÊ)</strong><br><br><br><br>
            (Ký và ghi rõ họ tên)<br>
            ......................................
        </td>
    </tr>
</table>
