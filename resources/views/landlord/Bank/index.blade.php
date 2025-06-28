@extends('landlord.layouts.app')
@section('title', 'Quản lý ngân hàng của tôi')
@section('content')
<h3>Quản lý ngân hàng của tôi</h3>

{{-- Form thêm tài khoản ngân hàng --}}
<div class="card mb-4">
    <div class="card-header">Thêm tài khoản ngân hàng</div>
    <div class="card-body">
        <form action="{{ route('landlords.bank_accounts.store') }}" method="post">
            @csrf
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <input name="bank_name" class="form-control" placeholder="Tên ngân hàng" required>
                </div>
                <div class="col-md-4">
                    <input name="bank_account_name" class="form-control" placeholder="Chủ tài khoản" required>
                </div>
                <div class="col-md-4">
                    <input name="bank_account_number" class="form-control" placeholder="Số tài khoản" required>
                </div>
            </div>
            <button class="btn btn-primary btn-sm">Thêm tài khoản</button>
        </form>
    </div>
</div>

{{-- Danh sách ngân hàng đã thêm --}}
<div class="card">
    <div class="card-header">Danh sách ngân hàng</div>
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Ngân hàng</th>
                    <th>Chủ tài khoản</th>
                    <th>Số TK</th>
                    <th style="width:150px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bankAccounts as $bank)
                    <tr>
                        <td>{{ $bank->id }}</td>
                        <td>{{ $bank->bank_name }}</td>
                        <td>{{ $bank->bank_account_name }}</td>
                        <td>{{ $bank->bank_account_number }}</td>
                        <td>
                            <!-- Nút Sửa -->
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $bank->id }}">Sửa</button>
                            <!-- Nút Xóa -->
                            <form action="{{ route('landlords.bank_accounts.destroy', $bank->id) }}" method="post" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>

                            <!-- Modal Sửa -->
                            <div class="modal fade" id="editModal{{ $bank->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Sửa tài khoản #{{ $bank->id }}</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('landlords.bank_accounts.update', $bank->id) }}" method="post">
                                                @csrf @method('PUT')
                                                <div class="mb-2">
                                                    <label>Ngân hàng</label>
                                                    <input name="bank_name" class="form-control" value="{{ $bank->bank_name }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label>Chủ tài khoản</label>
                                                    <input name="bank_account_name" class="form-control" value="{{ $bank->bank_account_name }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label>Số tài khoản</label>
                                                    <input name="bank_account_number" class="form-control" value="{{ $bank->bank_account_number }}" required>
                                                </div>
                                                <button class="btn btn-primary btn-sm">Cập nhật</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                @endforeach
                @if($bankAccounts->isEmpty())
                    <tr><td colspan="5" class="text-center text-muted">Chưa có tài khoản nào.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
