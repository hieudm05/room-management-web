<table>
    <thead>
        <tr>
            <th>Tòa nhà</th>
            <th>Phòng</th>
            <th>Khách thuê</th>
            <th>Tháng</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bills as $bill)
        <tr>
            <td>{{ $bill->room->property->name ?? 'N/A' }}</td>
            <td>{{ $bill->room->room_number }}</td>
            <td>{{ optional($bill->room->rentalAgreement->renter)->name ?? 'Chưa có' }}</td>
            <td>{{ $bill->month }}</td>
            <td>{{ number_format($bill->total) }}</td>
            <td>{{ ucfirst($bill->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
