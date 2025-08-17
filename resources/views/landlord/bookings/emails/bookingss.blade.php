<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Lịch hẹn xem phòng</title>
</head>

<body>
    <h2>Xin chào {{ $customer_name }},</h2>

    <p>Chủ trọ đã sắp xếp lịch hẹn để bạn đến xem phòng.</p>

    <p><strong>📅 Thời gian hẹn:</strong> {{ \Carbon\Carbon::parse($appointment_time)->format('d/m/Y H:i') }}</p>
    <p><strong>👤 Chủ trọ:</strong> {{ $landlord_name }}</p>
    <p><strong>📞 Số điện thoại:</strong> {{ $landlord_phone }}</p>
    <p><strong>📍 Địa điểm gặp mặt:</strong> {{ $landlord_address }}</p>

    <br>
    <p>Vui lòng đến đúng giờ. Nếu bạn không thể đến, hãy liên hệ trực tiếp với chủ trọ để hẹn lại.</p>

    <p>Trân trọng,<br>Hệ thống đặt phòng</p>
</body>

</html>
