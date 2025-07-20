@extends('landlord.layouts.app')

@section('title', 'List Properties')

@section('content')
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Danh Sách Bất Động Sản</h4>
                <a href="{{ route('landlords.properties.create') }}" class="btn btn-success btn-sm">
                    + Thêm Bất Động Sản Mới
                </a>
            </div>

            <div class="card-body">
                <div class="row gy-4">
                    @forelse ($listProperties as $Property)
                        @php
                            $status = $Property->status;
                            $badgeClass = match ($status) {
                                'Pending' => 'bg-warning',
                                'Approved' => 'bg-success',
                                'Rejected' => 'bg-danger',
                                'Suspended' => 'bg-secondary',
                                default => 'bg-light',
                            };
                        @endphp

                        <div class="col-md-3 col-sm-6">
                            <div class="card shadow-sm h-100">
                                {{-- Nếu bạn có hình ảnh của property thì đặt vào src, nếu không thì dùng placeholder --}}
                                <img src="https://via.placeholder.com/400x250?text=Building" class="card-img-top"
                                    alt="Property image">

                                <div class="card-body">
                                    <h5 class="card-title mb-1">{{ $Property->name }}</h5>
                                    <p class="text-muted small">{{ $Property->address }}</p>
                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                </div>

                                <div class="card-footer text-center bg-white border-0">
                                    <a href="{{ route('landlords.properties.shows', ['property_id' => $Property->property_id]) }}"
                                        class="btn btn-primary btn-sm w-100">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-danger">No properties have been listed.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3 d-flex justify-content-center">
        {{ $listProperties->links() }}
    </div>
@endsection
