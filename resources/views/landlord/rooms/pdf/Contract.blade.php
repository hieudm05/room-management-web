<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hợp đồng thuê phòng</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            line-height: 1.6;
            font-size: 14px;
        }

        .center {
            text-align: center;
        }

        ul {
            padding-left: 20px;
        }

        table {
            border-collapse: collapse;
        }
    </style>
</head>

<body>

    <div class="center">
        <p><strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong></p>
        <p><strong>Độc lập – Tự do – Hạnh phúc</strong></p>
        <h2><strong>HỢP ĐỒNG THUÊ PHÒNG TRỌ</strong></h2>
    </div>

    <p><strong>Hôm nay, ngày {{ $ngay_hop_dong ?? '..........' }} tháng {{ $thang_hop_dong ?? '..........' }} năm {{ $nam_hop_dong ?? '20....' }}</strong></p>

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
        <li>CMND/CCCD số: {{ $landlord->identity_number }}</li>
        <li>SĐT: {{ $landlord->phone_number }}</li>
        <li>Thường trú: {{ $landlord->address ?? '....................................................' }}</li>
    </ul>

    <h4>II. BÊN THUÊ PHÒNG TRỌ (Bên B):</h4>
    <ul>
        <li>Họ tên: {{ $ten_nguoi_thue }}</li>
        <li>CMND/CCCD số: {{ $cccd_nguoi_thue }}</li>
        <li>SĐT: {{ $sdt_nguoi_thue }}</li>
        <li>Email: {{ $email_nguoi_thue }}</li>
        <li>Số lượng người ở: {{ $so_luong_nguoi_o }}</li>
        <li>Số lượng người tối đa: {{ $so_luong_nguoi_toi_da }}</li>
    </ul>

    <h4>III. Nội dung hợp đồng:</h4>
    <ul>
        <li>Phòng thuê số: {{ $room_number }}</li>
        <li>Diện tích: {{ $dien_tich }} m²</li>
        <li>Giá thuê: {{ number_format($gia_thue) }} VNĐ / tháng</li>
        <li>Tiền cọc: {{ number_format($deposit_price) }} VNĐ</li>
    </ul>

    <h4>IV. Tiện nghi đi kèm:</h4>
    <ul>
        @foreach($facilities as $fac)
        <li>{{ $fac }}</li>
        @endforeach
    </ul>

    <h4>V. Dịch vụ:</h4>
    <ul>
        @foreach ($services as $service)
        <li>{{ $service['name'] }}: {{ $service['price'] }}</li>
        @endforeach
    </ul>

    <h4>VI. Thời gian hợp đồng:</h4>
    <ul>
        <li>Ngày bắt đầu: {{ $ngay_bat_dau }}</li>
        <li>Ngày kết thúc: {{ $ngay_ket_thuc }}</li>
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

</body>

</html>
