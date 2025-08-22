@extends('landlord.layouts.app')

@section('title', 'Hoá đơn tiền phòng')

@section('content')
    <div class="container my-4">
        <h3 class="mb-4">Danh sách tòa nhà bạn quản lý</h3>
        @if($properties->isEmpty())
            <div class="alert alert-warning">Bạn chưa quản lý tòa nhà nào.</div>
        @else
            <div class="row">
                @foreach($properties as $property)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $property->name }}</h5>
                                <p class="card-text">
                                    <strong>Địa chỉ:</strong> {{ $property->address }}<br>
                                    <strong>Mô tả:</strong> {{ $property->description ?? 'Không có mô tả' }}
                                </p>
                               <form action="{{ route('landlords.payment.index')}}" method="get" class="d-inline">
                                    <input type="hidden" name="property_id" value='{{ $property->property_id }}'>
                                    <button type="submit" class="btn btn-primary btn-sm">Nhập/Kiểm tra hoá đơn</button>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
@endsection        