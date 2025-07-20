@extends('landlord.layouts.app')

@section('title', 'Chi tiết yêu cầu chỉnh sửa')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Chi tiết yêu cầu chỉnh sửa phòng</h4>

    <div class="card">
        <div class="card-body">
            <p><strong>Phòng:</strong> {{ $requestEdit->room->room_number }}</p>
            <p><strong>Nhân viên:</strong> {{ $requestEdit->staff->name }}</p>
            <p><strong>Trạng thái:</strong> {{ ucfirst($requestEdit->status) }}</p>
            <p><strong>Thời gian gửi:</strong> {{ $requestEdit->created_at->format('d/m/Y H:i') }}</p>

            <hr>
            <h5>1. Thông tin cơ bản thay đổi:</h5>
            @if (!empty($changes))
                <ul>
                    @foreach ($changes as $key => $value)
                        <li>
                            <strong>{{ ucfirst($key) }}:</strong>
                            <span class="text-danger">{{ $value['old'] }}</span>
                            ➶ <span class="text-success">{{ $value['new'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Không có thay đổi về thông tin cơ bản.</p>
            @endif

            <hr>
            <h5>2. Thay đổi tiện nghi:</h5>
            @if (count($addedFacilities) || count($removedFacilities))
                @if (count($addedFacilities))
                    <p><strong class="text-success">+ Thêm:</strong> {{ implode(', ', $addedFacilities) }}</p>
                @endif
                @if (count($removedFacilities))
                    <p><strong class="text-danger">− Xoá:</strong> {{ implode(', ', $removedFacilities) }}</p>
                @endif
            @else
                <p class="text-muted">Không có thay đổi về tiện nghi.</p>
            @endif

            <hr>
            <h5>3. Thay đổi dịch vụ:</h5>
            @if (count($serviceChanges))
                <ul>
                    @foreach ($serviceChanges as $sid => $change)
                        <li>
                            <strong>{{ $change['name'] }}</strong><br>
                            <ul>
                                @if (isset($change['price']))
                                    <li>💰 Giá: <span class="text-danger">{{ $change['price']['old'] }}</span> ➶ <span class="text-success">{{ $change['price']['new'] }}</span></li>
                                @endif
                                @if (isset($change['unit']))
                                    <li>📏 Đơn vị: <span class="text-danger">{{ $change['unit']['old'] }}</span> ➶ <span class="text-success">{{ $change['unit']['new'] }}</span></li>
                                @endif
                                @if (isset($change['enabled']))
                                    <li>⚙️ Trạng thái: 
                                        @if ($change['enabled']['old'] === false)
                                            <span class="text-success">Bật</span>
                                        @else
                                            <span class="text-danger">Tắt</span>
                                        @endif
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Không có thay đổi về dịch vụ.</p>
            @endif

            <hr>
            <h5>4. Ảnh bị yêu cầu xoá:</h5>
            @if (count($deletedPhotos))
                <div class="row">
                    @foreach ($deletedPhotos as $photo)
                        <div class="col-md-2 text-center">
                            <img src="{{ $photo->image_url }}" class="img-thumbnail mb-1" style="max-height: 120px;">
                            <p class="text-danger small">Bị yêu cầu xoá</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">Không có ảnh nào bị xoá.</p>
            @endif

            <hr>
            <h5>5. Ảnh mới được thêm:</h5>
            @if (!empty($newPhotoNames))
                <ul>
                    @foreach ($newPhotoNames as $name)
                        <li>📷 {{ $name }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Không có ảnh mới được thêm.</p>
            @endif

            @if ($requestEdit->status === 'pending')
                <hr>
                <form method="POST" action="{{ route('landlords.room_edit_requests.approve', $requestEdit->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">✔️ Duyệt</button>
                </form>

                <form method="POST" action="{{ route('landlords.room_edit_requests.reject', $requestEdit->id) }}" class="d-inline ms-2">
                    @csrf
                    <input type="text" name="note" class="form-control d-inline w-25" placeholder="Lý do từ chối">
                    <button type="submit" class="btn btn-danger mt-1">❌ Từ chối</button>
                </form>
            @elseif($requestEdit->status === 'rejected')
                <div class="alert alert-warning mt-3">
                    <strong>Lý do từ chối:</strong> {{ $requestEdit->note ?? 'Không có ghi chú.' }}
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('landlords.room_edit_requests.index') }}" class="btn btn-secondary">⬅️ Quay lại danh sách</a>
            </div>
        </div>
    </div>
</div>
@endsection