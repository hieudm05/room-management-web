<h2>Thông báo khách thuê chuyển phòng</h2>

<p><strong>Khách thuê:</strong> {{ $tenant['full_name'] ?? '' }}</p>
<p><strong>Email:</strong> {{ $tenant['email'] ?? '' }}</p>
<p><strong>SĐT:</strong> {{ $tenant['phone'] ?? '' }}</p>
<p><strong>CCCD:</strong> {{ $tenant['cccd'] ?? '' }}</p>

<p><strong>Phòng mới:</strong> {{ $room->room_number }}</p>
<p><strong>Khu trọ:</strong> {{ $room->property->name }}</p>
