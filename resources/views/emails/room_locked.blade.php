@php
    use Illuminate\Support\Facades\Storage;
@endphp

<h2>Xin chào,</h2>
<p>
    Phòng <strong>{{ $room->room_number }}</strong> bạn đang thuê tại khu trọ
    <strong>{{ $room->property->name }}</strong> đã bị khóa.
</p>
<p><strong>Lý do:</strong> {{ $reason }}</p>

@if ($suggestedRooms->count())
    <p>Chúng tôi gợi ý cho bạn vài phòng khác hiện đang trống:</p>
    <ul>
        @foreach ($suggestedRooms as $suggested)
            <li style="margin-bottom:15px;">
                <strong>Khu trọ:</strong> {{ $suggested->property->name }} <br>
                <strong>Phòng:</strong> {{ $suggested->room_number }} <br>
                <strong>Diện tích:</strong> {{ $suggested->area }} m² <br>
                <strong>Số người ở tối đa:</strong> {{ $suggested->occupants }} <br>
                <strong>Giá cọc:</strong> {{ number_format($suggested->deposit) }} VND <br>
                <strong>Giá thuê:</strong> {{ number_format($suggested->rental_price) }} VND <br>

                <strong>Tiện nghi:</strong>
                @if ($suggested->facilities->count())
                    {{ $suggested->facilities->pluck('name')->implode(', ') }}
                @else
                    Không có
                @endif
                <br>

                <strong>Dịch vụ:</strong>
                @if ($suggested->services->count())
                    {{ $suggested->services->pluck('name')->implode(', ') }}
                @else
                    Không có
                @endif
                <br>

                <strong>Ảnh:</strong><br>
                @if ($suggested->photos->count())
                    @foreach ($suggested->photos->take(5) as $photo)
                        @php
                            // Xử lý bỏ "storage/" nếu có trong DB
                            $cleanPath = str_replace('storage/', '', $photo->image_url);

                            // Build path tuyệt đối
                            $imagePath = public_path('storage/' . ltrim($cleanPath, '/'));
                        @endphp

                        @if (file_exists($imagePath))
                            <img src="{{ $message->embed($imagePath) }}" width="120"
                                style="margin:5px;border-radius:8px;">
                        @else
                            <p>[Ảnh không tồn tại]</p>
                        @endif
                    @endforeach
                @else
                    Chưa có ảnh
                @endif

                <br><br>
                {{-- ✅ Nút tham gia phòng --}}
                @php
                    $lastTerminated = $suggested
                        ->rentalAgreements()
                        ->where('status', \App\Models\RentalAgreement::STATUS_TERMINATED)
                        ->latest('updated_at')
                        ->first();
                    $agreementId = $lastTerminated->rental_id ?? null;
                @endphp

                <a
                    href="{{ route('client.rooms.join', ['room' => $suggested->room_id, 'agreement' => $agreementId]) }}">
                    ✅ Tham gia phòng này
                </a>


            </li>
        @endforeach
    </ul>
@endif

<p>Xin cảm ơn bạn đã thông cảm.</p>
