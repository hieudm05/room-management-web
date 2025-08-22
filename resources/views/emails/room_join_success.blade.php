<h2>Xin chào,</h2>
<p>Bạn đã tham gia thành công phòng <strong>{{ $room->room_number }}</strong> tại khu trọ <strong>{{ $room->property->name }}</strong>.</p>

<p>
    <strong>Diện tích:</strong> {{ $room->area }} m² <br>
    <strong>Số người ở tối đa:</strong> {{ $room->occupants }} <br>
    <strong>Giá thuê:</strong> {{ number_format($room->rental_price) }} VND <br>
    <strong>Giá cọc:</strong> {{ number_format($room->deposit) }} VND <br>
</p>

<p>Chúc bạn có trải nghiệm tốt tại phòng mới!</p>
