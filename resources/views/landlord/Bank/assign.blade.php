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
                            @forelse ($bankAccounts as $bank)
                                <div class="form-check mb-2">
                                    <input class="form-check-input bank-radio" type="radio" name="bank_account_id"
                                        id="bank_{{ $bank->id }}" value="{{ $bank->id }}" required>
                                    <label class="form-check-label" for="bank_{{ $bank->id }}">
                                        <strong>{{ $bank->bank_name }}</strong> - {{ $bank->bank_account_number }}<br>
                                        <small>{{ $bank->bank_account_name }} - <span
                                                class="text-primary">{{ $bank->owner_name }}</span></small>
                                    </label>
                                </div>
                            @empty
                                <div class="text-muted text-center py-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Chưa có tài khoản ngân hàng nào. 
                                    <a href="{{ route('landlords.bank_accounts.index') }}" class="text-decoration-none">
                                        Thêm tài khoản ngân hàng
                                    </a>
                                </div>
                            @endforelse
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
                            <div class="row" id="properties-container">
                                @forelse ($properties as $property)
                                    @php
                                        $isAssigned = $property->bank_account_id ? true : false;
                                        $bankStatus = null;
                                        $bankInfo = '';
                                        
                                        if ($isAssigned && $property->bankAccount) {
                                            $bankStatus = $property->bankAccount->status;
                                            $bankInfo = $property->bankAccount->bank_name . ' - ' . $property->bankAccount->bank_account_number;
                                        }
                                    @endphp
                                    <div class="col-md-6 mb-2">
                                        <div
                                            class="form-check p-2 rounded property-item
                                        @if($isAssigned && $bankStatus == 'active') 
                                            bg-success bg-opacity-10 border-success
                                        @elseif($isAssigned && $bankStatus == 'inactive')
                                            bg-danger bg-opacity-10 border-danger
                                        @else
                                            bg-secondary bg-opacity-10 border-secondary
                                        @endif"
                                            data-bank-id="{{ $property->bank_account_id }}"
                                            data-bank-status="{{ $bankStatus }}">
                                            <input class="form-check-input property-checkbox" type="checkbox"
                                                name="property_ids[]" value="{{ $property->property_id }}"
                                                id="property_{{ $property->property_id }}"
                                                {{ $isAssigned && $bankStatus == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="property_{{ $property->property_id }}">
                                                {{ $property->name }}
                                                @if ($isAssigned && $bankStatus == 'active')
                                                    <span class="badge bg-success ms-1">Đã gán</span>
                                                @elseif ($isAssigned && $bankStatus == 'inactive')
                                                    <span class="badge bg-danger ms-1">TK bị khóa</span>
                                                @else
                                                    <span class="badge bg-secondary ms-1">Chưa gán</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $property->address }}</small>
                                                @if ($bankInfo)
                                                    <br>
                                                    <small class="text-info">{{ $bankInfo }}</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted text-center py-3">
                                        <i class="fas fa-building me-2"></i>
                                        Chưa có tòa nhà nào.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-4" 
                                {{ $bankAccounts->isEmpty() ? 'disabled' : '' }}>
                            Gán tài khoản
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAllBox = document.getElementById('checkAll');
            const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
            const bankRadios = document.querySelectorAll('.bank-radio');
            const propertiesContainer = document.getElementById('properties-container');

            // Xử lý chọn tất cả
            if (checkAllBox) {
                checkAllBox.addEventListener('change', function() {
                    propertyCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                // Cập nhật trạng thái "Chọn tất cả" khi có thay đổi checkbox con
                propertyCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const totalCheckboxes = propertyCheckboxes.length;
                        const checkedCheckboxes = document.querySelectorAll('.property-checkbox:checked').length;
                        
                        checkAllBox.checked = totalCheckboxes === checkedCheckboxes;
                        checkAllBox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
                    });
                });
            }

            // Khi chọn tài khoản ngân hàng, hiển thị các tòa nhà đã gán cho tài khoản đó
            bankRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        const selectedBankId = this.value;
                        updatePropertySelection(selectedBankId);
                    }
                });
            });

            function updatePropertySelection(bankId) {
                const propertyItems = document.querySelectorAll('.property-item');
                
                propertyItems.forEach(item => {
                    const checkbox = item.querySelector('.property-checkbox');
                    const badge = item.querySelector('.badge');
                    const itemBankId = item.dataset.bankId;
                    const itemBankStatus = item.dataset.bankStatus;
                    
                    // Reset trạng thái
                    item.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'bg-secondary', 'bg-opacity-10', 
                                         'border-success', 'border-warning', 'border-danger', 'border-secondary');
                    
                    if (itemBankId && itemBankId === bankId) {
                        // Tòa nhà đang được gán cho tài khoản được chọn
                        checkbox.checked = true;
                        item.classList.add('bg-success', 'bg-opacity-10', 'border-success');
                        badge.textContent = 'Đã gán';
                        badge.className = 'badge bg-success ms-1';
                    } else if (itemBankId && itemBankId !== bankId) {
                        // Tòa nhà đang được gán cho tài khoản khác
                        checkbox.checked = false;
                        
                        if (itemBankStatus === 'inactive') {
                            // Tài khoản khác bị khóa
                            item.classList.add('bg-danger', 'bg-opacity-10', 'border-danger');
                            badge.textContent = 'TK bị khóa';
                            badge.className = 'badge bg-danger ms-1';
                        } else {
                            // Tài khoản khác đang hoạt động
                            item.classList.add('bg-warning', 'bg-opacity-10', 'border-warning');
                            badge.textContent = 'Có TK khác';
                            badge.className = 'badge bg-warning ms-1';
                        }
                    } else {
                        // Tòa nhà chưa được gán
                        checkbox.checked = false;
                        item.classList.add('bg-secondary', 'bg-opacity-10', 'border-secondary');
                        badge.textContent = 'Chưa gán';
                        badge.className = 'badge bg-secondary ms-1';
                    }
                });

                // Cập nhật trạng thái "Chọn tất cả"
                const totalCheckboxes = propertyCheckboxes.length;
                const checkedCheckboxes = document.querySelectorAll('.property-checkbox:checked').length;
                
                if (checkAllBox) {
                    checkAllBox.checked = totalCheckboxes === checkedCheckboxes;
                    checkAllBox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
                }
            }

            // Khởi tạo trạng thái ban đầu nếu có tài khoản được chọn sẵn
            const selectedBank = document.querySelector('.bank-radio:checked');
            if (selectedBank) {
                updatePropertySelection(selectedBank.value);
            }
        });
    </script>
@endpush