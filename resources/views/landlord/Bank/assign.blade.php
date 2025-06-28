{{-- resources/views/landlord/Bank/assign.blade.php --}}
@extends('landlord.layouts.app')
@section('title', 'Gán tài khoản ngân hàng cho các tòa')

@section('content')
    <div class="container">
        {{-- Thông báo --}}
        <div id="alert-area" class="position-fixed top-0 start-50 translate-middle-x mt-3"
            style="z-index: 1055; min-width:300px; max-width:90vw;">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <div class="row">
            <form action="{{ route('landlords.bank_accounts.assign.store') }}" method="POST" class="row">
                @csrf
                <div class="col-md-5">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-primary text-white fw-bold">Chọn tài khoản ngân hàng</div>
                        <div class="card-body">
                            @foreach ($bankAccounts as $bank)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="bank_account_id"
                                        id="bank_{{ $bank->id }}" value="{{ $bank->id }}" required>
                                    <label class="form-check-label" for="bank_{{ $bank->id }}">
                                        <strong>{{ $bank->bank_name }}</strong> - {{ $bank->bank_account_number }}<br>
                                        <small>{{ $bank->bank_account_name }}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card shadow-sm mb-3">
                        <div
                            class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                            <span>Chọn các tòa/khu trọ để gán</span>
                            <div>
                                <input type="checkbox" id="checkAll" class="form-check-input">
                                <label for="checkAll" class="form-check-label ms-1">Chọn tất cả</label>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <div class="row">
                                @foreach ($properties as $property)
                                    @php
                                        $isAssigned = $property->bank_account_id ? true : false;
                                    @endphp
                                    <div class="col-md-6 mb-2">
                                        <div
                                            class="form-check p-2 rounded
                                        {{ $isAssigned ? 'bg-success bg-opacity-10 border-success' : 'bg-secondary bg-opacity-10 border-secondary' }}">
                                            <input class="form-check-input property-checkbox" type="checkbox"
                                                name="property_ids[]" value="{{ $property->property_id }}"
                                                id="property_{{ $property->property_id }}"
                                                {{ $isAssigned ? 'checked' : '' }}>
                                            <label class="form-check-label" for="property_{{ $property->property_id }}">
                                                {{ $property->name }}
                                                @if ($isAssigned)
                                                    <span class="badge bg-success ms-1">Đã gán</span>
                                                @else
                                                    <span class="badge bg-secondary ms-1">Chưa gán</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $property->address }}</small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-4">Gán tài khoản</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('checkAll').addEventListener('change', function() {
            let checked = this.checked;
            document.querySelectorAll('.property-checkbox').forEach(cb => cb.checked = checked);
        });
        setTimeout(function() {
            let alertNode = document.querySelector('#alert-area .alert');
            if (alertNode) {
                let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                bsAlert.close();
            }
        }, 3000);

        document.getElementById('checkAll').addEventListener('change', function() {
            let checked = this.checked;
            document.querySelectorAll('.property-checkbox').forEach(cb => cb.checked = checked);
        });
    </script>
@endpush
