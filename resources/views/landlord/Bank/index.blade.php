@extends('landlord.layouts.app')
@section('title', 'Quản lý ngân hàng của tôi')
@section('content')


    <h3>Quản lý ngân hàng của tôi</h3>
    {{-- Nút thêm tài khoản --}}
    <div class="mb-3 d-flex gap-2">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBankModalLandlord">
            Thêm tài khoản cho tôi
        </button>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addBankModalStaff">
            Thêm tài khoản cho quản lý
        </button>
    </div>
    {{-- Danh sách tài khoản ngân hàng --}}
    <div class="card">



        {{-- Modal: Thêm tài khoản cho chủ trọ --}}
        <div class="modal fade" id="addBankModalLandlord" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form action="{{ route('landlords.bank_accounts.store') }}" method="post" class="modal-content"
                    autocomplete="off">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm tài khoản cho bạn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3 position-relative">
                                <label>Ngân hàng</label>
                                <input id="bank_name_landlord" name="bank_name" class="form-control"
                                    placeholder="Tên ngân hàng" autocomplete="off" required>
                                <input type="hidden" id="bank_code_landlord" name="bank_code">
                                <div id="bank_suggestions_landlord" class="list-group position-absolute w-100"
                                    style="z-index:1080;"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Chủ tài khoản</label>
                                <input name="bank_account_name" class="form-control" placeholder="Chủ tài khoản" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Số tài khoản</label>
                                <input name="bank_account_number" class="form-control" placeholder="Số tài khoản" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm">Thêm</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Thêm tài khoản cho quản lý --}}
        <div class="modal fade" id="addBankModalStaff" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form action="{{ route('landlords.bank_accounts.staff.store') }}" method="post" class="modal-content"
                    autocomplete="off">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm tài khoản cho quản lý</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Chọn quản lý</label>
                                <select name="staff_id" class="form-select" required>
                                    <option value="">-- Chọn quản lý --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 position-relative">
                                <label>Ngân hàng</label>
                                <input id="bank_name_staff" name="bank_name" class="form-control"
                                    placeholder="Tên ngân hàng" autocomplete="off" required>
                                <input type="hidden" id="bank_code_staff" name="bank_code">
                                <div id="bank_suggestions_staff" class="list-group position-absolute w-100"
                                    style="z-index:1080;"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Chủ tài khoản</label>
                                <input name="bank_account_name" class="form-control" placeholder="Chủ tài khoản" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Số tài khoản</label>
                                <input name="bank_account_number" class="form-control" placeholder="Số tài khoản" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success btn-sm">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-header">Danh sách tài khoản ngân hàng</div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Ngân hàng</th>
                        <th>Chủ tài khoản</th>
                        <th>Số TK</th>
                        <th>Loại chủ tài khoản</th>
                        <th>Trạng thái</th>
                        <th style="width:150px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Tài khoản của chủ trọ --}}
                    @foreach ($bankAccounts as $bank)
                        <tr>
                            <td>{{ $bank->id }}</td>
                            <td>{{ $bank->bank_name }}</td>
                            <td>{{ $bank->bank_account_name }}</td>
                            <td>{{ $bank->bank_account_number }}</td>
                            <td><span class="badge bg-primary">Chủ trọ</span></td>
                            <td>
                                @if ($bank->status == 'active')
                                    <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Đã khóa</span>
                                @endif
                            </td>
                            <td>
                                <!-- Nút Sửa -->
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $bank->id }}">Sửa</button>
                                <!-- Modal Sửa -->
                                <div class="modal fade" id="editModal{{ $bank->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Sửa trạng thái tài khoản #{{ $bank->id }}</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('landlords.bank_accounts.update', $bank->id) }}"
                                                method="post">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Trạng thái</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="active" @selected($bank->status == 'active')>Đang hoạt
                                                                động</option>
                                                            <option value="inactive" @selected($bank->status == 'inactive')>Đã khóa
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-primary btn-sm">Cập nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    {{-- Tài khoản của các quản lý --}}
                    @foreach ($staffs as $staff)
                        @foreach ($staff->bankAccounts as $bank)
                            <tr>
                                <td>{{ $bank->id }}</td>
                                <td>{{ $bank->bank_name }}</td>
                                <td>{{ $bank->bank_account_name }}</td>
                                <td>{{ $bank->bank_account_number }}</td>
                                <td>
                                    <span class="badge bg-success">Quản lý</span>
                                    <br>
                                    <small>{{ $staff->name }} ({{ $staff->email }})</small>
                                </td>
                                <td>
                                    @if ($bank->status == 'active')
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Đã khóa</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Nút Sửa -->
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $bank->id }}">Sửa</button>
                                    <!-- Modal Sửa -->
                                    <div class="modal fade" id="editModal{{ $bank->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Sửa trạng thái tài khoản #{{ $bank->id }}
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('landlords.bank_accounts.update', $bank->id) }}"
                                                    method="post">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label>Trạng thái</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="active" @selected($bank->status == 'active')>Đang
                                                                    hoạt
                                                                    động</option>
                                                                <option value="inactive" @selected($bank->status == 'inactive')>Đã
                                                                    khóa
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary btn-sm">Cập nhật</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach

                    @if ($bankAccounts->isEmpty() && $staffs->every(fn($s) => $s->bankAccounts->isEmpty()))
                        <tr>
                            <td colspan="6" class="text-center text-muted">Chưa có tài khoản nào.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <style>
        #bank_suggestions_landlord,
        #bank_suggestions_staff {
            max-height: 250px !important;
            overflow-y: auto !important;
            z-index: 1080 !important;
        }
    </style>

    <script>
        let banks = [];
        fetch('https://api.vietqr.io/v2/banks')
            .then(res => res.json())
            .then(data => banks = data.data);

        // Autocomplete cho chủ trọ
        document.getElementById('bank_name_landlord').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            const suggestions = banks.filter(b => b.name.toLowerCase().includes(val) || b.shortName.toLowerCase()
                .includes(val));
            let html = '';
            suggestions.slice(0, 8).forEach(b => {
                html +=
                    `<button type="button" class="list-group-item list-group-item-action" data-code="${b.code}" data-name="${b.name}">${b.name} (${b.code})</button>`;
            });
            const sug = document.getElementById('bank_suggestions_landlord');
            sug.innerHTML = html;
            sug.style.display = html ? 'block' : 'none';
        });
        document.getElementById('bank_suggestions_landlord').addEventListener('click', function(e) {
            if (e.target.dataset.code) {
                document.getElementById('bank_name_landlord').value = e.target.dataset.name;
                document.getElementById('bank_code_landlord').value = e.target.dataset.code;
                this.innerHTML = '';
                this.style.display = 'none';
            }
        });
        document.addEventListener('click', function(e) {
            if (!document.getElementById('bank_name_landlord').contains(e.target) &&
                !document.getElementById('bank_suggestions_landlord').contains(e.target)) {
                document.getElementById('bank_suggestions_landlord').innerHTML = '';
                document.getElementById('bank_suggestions_landlord').style.display = 'none';
            }
        });

        // Autocomplete cho quản lý
        document.getElementById('bank_name_staff').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            const suggestions = banks.filter(b => b.name.toLowerCase().includes(val) || b.shortName.toLowerCase()
                .includes(val));
            let html = '';
            suggestions.slice(0, 8).forEach(b => {
                html +=
                    `<button type="button" class="list-group-item list-group-item-action" data-code="${b.code}" data-name="${b.name}">${b.name} (${b.code})</button>`;
            });
            const sug = document.getElementById('bank_suggestions_staff');
            sug.innerHTML = html;
            sug.style.display = html ? 'block' : 'none';
        });
        document.getElementById('bank_suggestions_staff').addEventListener('click', function(e) {
            if (e.target.dataset.code) {
                document.getElementById('bank_name_staff').value = e.target.dataset.name;
                document.getElementById('bank_code_staff').value = e.target.dataset.code;
                this.innerHTML = '';
                this.style.display = 'none';
            }
        });
        document.addEventListener('click', function(e) {
            if (!document.getElementById('bank_name_staff').contains(e.target) &&
                !document.getElementById('bank_suggestions_staff').contains(e.target)) {
                document.getElementById('bank_suggestions_staff').innerHTML = '';
                document.getElementById('bank_suggestions_staff').style.display = 'none';
            }
        });
    </script>

@endsection
