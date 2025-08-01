<h2>🔔 Phòng {{ $room->room_number }} đã được cập nhật</h2>

<p>Xin chào,</p>

<p>Phòng đã có các thay đổi như sau:</p>

<ul>
    @foreach ($changes as $field => $value)
        <li>
            <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
            {{ $value['old'] }} → {{ $value['new'] }}
        </li>
    @endforeach
</ul>

<p>Vui lòng kiểm tra lại thông tin nếu có thắc mắc.</p>

<p>Trân trọng,<br>Hệ thống quản lý phòng trọ</p>
