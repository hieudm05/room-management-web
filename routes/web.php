<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomBillController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\PostController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\Landlord\BookingsChart;
use App\Http\Controllers\Landlord\OCRController;
use App\Http\Controllers\Client\MyRoomController;
use App\Http\Controllers\Landlord\RoomController;
use App\Http\Controllers\Renter\RoomLeaveController;
use App\Http\Controllers\Landlord\Staff\ElectricWaterController;
use App\Http\Controllers\TenantProfileController;
use App\Http\Controllers\Landlord\ChartController;
use App\Http\Controllers\Landlord\ComplaintsChart;
use App\Http\Controllers\Landlord\ContractRenewal;
use App\Http\Controllers\Client\AuthUserController;


use App\Http\Controllers\Landlord\ApprovalController;
use App\Http\Controllers\Landlord\BookingsController;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\Client\UserBookingController;
use App\Http\Controllers\Landlord\RoomStaffController;
use App\Http\Controllers\Client\AuthLandlordController;
use App\Http\Controllers\Client\ChangePasswordController;
use App\Http\Controllers\Client\ResetPasswordController;
use App\Http\Controllers\Landlord\BankAccountController;
use App\Http\Controllers\Client\ForgotPasswordController;

use App\Http\Controllers\Landlord\ApprovalUserController;
;
use App\Http\Controllers\Landlord\HomeLandlordController;
use App\Http\Controllers\Landlord\BillController;
use App\Http\Controllers\Landlord\LandlordBillController;
use App\Http\Controllers\Landlord\PostApprovalController;
use App\Http\Controllers\Landlord\StaffAccountController;
use App\Http\Controllers\Landlord\StaffBookingController;
use App\Http\Controllers\Renter\AddUserRequestController;
use App\Http\Controllers\Landlord\Staff\PaymentController;
use App\Http\Controllers\Landlord\Staff\ServiceController;
use App\Http\Controllers\Renter\DashboardRenterController;
use App\Http\Controllers\Renter\RenterComplaintController;
use App\Http\Controllers\Landlord\Staff\ContractController;
use App\Http\Controllers\Landlord\Staff\DocumentController;
use App\Http\Controllers\Landlord\ComplaintsChartController;
use App\Http\Controllers\Landlord\ContractRenewalController;

use App\Http\Controllers\Landlord\LandlordBankAccountController;
use App\Http\Controllers\LandLord\LandLordComplaintController;
use App\Http\Controllers\Landlord\PropertyBankAccountController;
use App\Http\Controllers\Landlord\Staff\StaffRoomEditController;
use App\Http\Controllers\Landlord\landLordNotificationController;
use App\Http\Controllers\Landlord\LandlordRoomLeaveController;
use App\Http\Controllers\Landlord\Staff\StaffComplaintController;
use App\Http\Controllers\Landlord\Staff\StaffRoomLeaveController;
use App\Http\Controllers\Landlord\PropertyRoomBankAccountController;

use App\Http\Controllers\Landlord\Staff\StaffNotificationController;
use App\Http\Controllers\Landlord\RoomEditRequestController;
use App\Http\Controllers\Landlord\Staff\StaffPostController;
use App\Http\Controllers\Landlord\Staff\StaffRoomController;
use App\Http\Controllers\Renter\RenterHistoryBillController;
use App\Http\Controllers\Renter\RenterNotificationController;



Route::get('/provinces', [AddressController::class, 'getProvinces']);
Route::get('/districts/{provinceCode}', [AddressController::class, 'getDistricts']);
Route::get('/wards/{districtCode}', [AddressController::class, 'getWards']);

// Auth chung
Route::get('/login', [AuthUserController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthUserController::class, 'login'])->name('login.post');

// LANDLORD
Route::prefix('landlords')->name('landlords.')->middleware(['auth'])->group(function () {

    Route::get('/', [HomeLandLordController::class, 'index'])->name('dashboard');
    Route::get('/filter-stats', [HomeLandLordController::class, 'filterStats'])->name('filter-stats');
    Route::get('/register', [AuthLandlordController::class, 'showForm'])->name('register.form');
    Route::post('/register', [AuthLandlordController::class, 'submit'])->name('register.submit');
    // Duyệt hợp đồng
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::delete('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    // Duyệt thêm người
    Route::get('/approvals/users', [ApprovalUserController::class, 'index'])->name('approvals.users.index');

    Route::post('/approvals/users/{id}/approve', [ApprovalUserController::class, 'approveUser'])
        ->whereNumber('id')
        ->name('approvals.users.approve');

    Route::delete('/approvals/users/{id}/reject', [ApprovalUserController::class, 'reject'])
        ->whereNumber('id')
        ->name('approvals.users.reject');

    // Duyệt hợp đồng
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');

    // Duyệt hợp đồng (theo approval_id)
    Route::post('/approvals/{id}/approve-contract', [ApprovalController::class, 'approveContract'])
        ->whereNumber('id')
        ->name('approvals.approve.contract');

    // Duyệt minh chứng đặt cọc (theo approval_id)
    Route::post('/approvals/{id}/approve-deposit', [ApprovalController::class, 'approveDeposit'])
        ->whereNumber('id')
        ->name('approvals.approve.deposit');

    Route::delete('/approvals/{id}/reject', [ApprovalController::class, 'reject'])
        ->whereNumber('id')
        ->name('approvals.reject');

    // Danh sách tài khoản của staff và thêm staff
    Route::get('staff_accounts', [StaffAccountController::class, 'index'])->name('staff_accounts.index');
    Route::get('staff_accounts-create', [StaffAccountController::class, 'create'])->name('staff_accounts.create');
    // Route::get('staff_accounts-create-test', [StaffAccountController::class, 'create'])->name('staff_accounts.create-test');
    Route::post('staff-accounts/store', [StaffAccountController::class, 'store'])->name('staff_accounts.store');
    Route::get('staff-accounts/{id}', [StaffAccountController::class, 'show'])->name('staff_accounts.show');

    // OCR CCCD
    Route::post('staff/ocr/identity-number', [OCRController::class, 'recognize'])->name('ocr.identity_number');

    // Properties
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/list', [PropertyController::class, 'index'])->name('list');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::post('/store', [PropertyController::class, 'store'])->name('store');
        Route::get('/{property_id}/edit', [PropertyController::class, 'edit'])->name('edit');
        Route::post('/{property_id}/update', [PropertyController::class, 'update'])->name('update');
        Route::get('/show/{property_id}', [PropertyController::class, 'show'])->name('show');
        Route::post('/{property_id}/upload-document', [PropertyController::class, 'uploadDocument'])->name('uploadDocument.post');
        Route::get('/{property_id}/shows', [PropertyController::class, 'showDetalShow'])->name('shows');
        Route::put('/{property_id}/bank-account', [PropertyBankAccountController::class, 'update'])->name('bank_accounts.update');
        Route::put('/{property_id}/bank-account/unassign', [PropertyBankAccountController::class, 'unassign'])->name('bank_accounts.unassign');
        Route::get('/{property}/bills/export', [PropertyController::class, 'exportBillsByMonth'])->name('bills.export');
    });

    Route::get('/bank-accounts/assign', [LandlordBankAccountController::class, 'assignToProperties'])->name('bank_accounts.assign');
    Route::post('/bank-accounts/assign', [LandlordBankAccountController::class, 'assignToPropertiesStore'])->name('bank_accounts.assign.store');

    // Gán tài khoản cho staff
    Route::post('/staff/store', [LandlordBankAccountController::class, 'storeForStaff'])->name('bank_accounts.staff.store');

    Route::prefix('bank-accounts')->name('bank_accounts.')->group(function () {
        Route::get('/', [LandlordBankAccountController::class, 'index'])->name('index');
        Route::post('/store', [LandlordBankAccountController::class, 'store'])->name('store');
        Route::put('/{id}', [LandlordBankAccountController::class, 'update'])->name('update');
        Route::delete('/{id}', [LandlordBankAccountController::class, 'destroy'])->name('destroy');
    });
    // Dịch vụ
    Route::resource('services', \App\Http\Controllers\Landlord\ServiceController::class);
    Route::patch('services/{service}/hide', [\App\Http\Controllers\Landlord\ServiceController::class, 'hide'])->name('services.hide');
    Route::patch('services/{service}/unhide', [\App\Http\Controllers\Landlord\ServiceController::class, 'unhide'])->name('services.unhide');
    Route::get('services-hidden', [\App\Http\Controllers\Landlord\ServiceController::class, 'hidden'])->name('services.hidden');
    Route::patch('services/{service}/toggle', [\App\Http\Controllers\Landlord\ServiceController::class, 'toggle'])->name('services.toggle');


    // Tiện nghi
    Route::resource('facilities', \App\Http\Controllers\Landlord\FacilityController::class);

    // Rooms
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/store', [RoomController::class, 'store'])->name('store');
        Route::post('/{room}/lock', [RoomController::class, 'lockContract'])->name('lockContract');
        // thống kê biểu đồ phòng
        Route::get('/{room}/statistics', [RoomBillController::class, 'showRoomStatistics'])->name('statistics');
        Route::get('/{room}/compare-months', [RoomBillController::class, 'compareMonths'])->name('compareMonths');
        Route::get('/{room}/month-detail', [RoomBillController::class, 'monthDetail'])->name('monthDetail');
        Route::get('/{room}/quarter-detail', [RoomBillController::class, 'quarterDetail'])->name('quarterDetail');
        Route::get('/{room}/compare-quarters', [RoomBillController::class, 'compareQuarters'])->name('compareQuarters');
        //
        Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{room}', [RoomController::class, 'update'])->name('update');
        Route::put('/{room}/hide', [RoomController::class, 'hide'])->name('hide');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
        Route::get('/{room}', [RoomController::class, 'show'])->name('show');
        Route::get('/{room}/contract-pdf', [RoomController::class, 'streamContract'])->name('contract.pdf');
        Route::get('/{room}/contract-download', [RoomController::class, 'downloadContract'])->name('contract.download');
        Route::get('/{room}/contract', [RoomController::class, 'contractIndex'])->name('contract.contractIndex');
        Route::post('/{room}/contract-upload', [RoomController::class, 'uploadContract'])->name('contract.upload');
        Route::get('/{room}/contracts/create', [RoomController::class, 'showForm'])->name('contracts.create');
        Route::post('/{room}/contracts/generate', [RoomController::class, 'generate'])->name('contracts.generate');
        Route::post('/{room}/contract-confirm', [RoomController::class, 'confirmContract2'])->name('contract.confirm');
        Route::get('/{room}/contract-word', [RoomController::class, 'downloadContractWord'])->name('contract.word');
        Route::get('/{room}/contract-form', [RoomController::class, 'formShowContract'])->name('contract.info');
        Route::post('/{room}/contract-confirm-rentalAgreement', [RoomController::class, 'confirmStatusrentalAgreement'])->name('contract.confirmLG');
        Route::post('/room-users/{id}/suscess', [RoomController::class, 'ConfirmAllUser'])->name('room_users.suscess');
        Route::get('/{room}/staffs', [RoomStaffController::class, 'edit'])->name('staffs.edit');
        Route::post('/{room}/staffs', [RoomStaffController::class, 'update'])->name('staffs.update');
        Route::post('{room}/kick', [RoomController::class, 'kickTenants'])
    ->name('rooms.kick');
        // Deposit (minh chứng đặt cọc)
        Route::get('/{room}/deposit', [RoomController::class, 'showDepositForm'])
            ->name('deposit.form');

        Route::post('/{room}/deposit', [RoomController::class, 'uploadDeposit'])
            ->name('deposit.upload');
    });

    // Staff quản lý phòng
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffRoomController::class, 'index'])->name('index');
        Route::get('/{room}/show', [StaffRoomController::class, 'show'])->name('show');


        Route::prefix('contract')->name('contract.')->group(function () {
            Route::get('/{room}', [ContractController::class, 'index'])->name('index');
            Route::post('/{room}/upload', [ContractController::class, 'uploadAgreementFile'])->name('upload');
            Route::post('/{room}/preview', [ContractController::class, 'preview'])->name('preview');
            Route::post('/{room}/confirm', [ContractController::class, 'confirm'])->name('confirm');
            Route::get('/{room}/form', [ContractController::class, 'showForm'])->name('form');
            Route::post('/{room}/generate', [ContractController::class, 'generate'])->name('generate');
        });

        // Form upload đặt cọc
        Route::get('/{room}/deposit', [StaffRoomController::class, 'depositForm'])->name('deposit.form');
        Route::post('/{room}/deposit-upload', [StaffRoomController::class, 'depositUpload'])->name('deposit.upload');

        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/{room}', [ServiceController::class, 'index']);
        });

        Route::prefix('electric-water')->name('electric_water.')->group(function () {
            Route::get('/{room}', [ElectricWaterController::class, 'index']);
            Route::post('/room-utility/{room}', [ElectricWaterController::class, 'store'])->name('store');
        });

        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/{room}', [DocumentController::class, 'index']);
        });

        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/', [PaymentController::class, 'list'])->name('list');
            Route::get('/list', [PaymentController::class, 'index'])->name('index');
            Route::post('/{room}', [PaymentController::class, 'store'])->name('store');
            Route::get('/{room}/export', [PaymentController::class, 'exportExcel'])->name('exportExcel');
            Route::post('/room-bills/{id}/update-status', [PaymentController::class, 'updateStatus']);
        });
    });
    // Bill của chủ trọ
    Route::get('/bills', [LandlordBillController::class, 'index'])->name('bills.index');
    Route::get('/bills/{bill}', [LandlordBillController::class, 'show'])->name('bills.show');
    Route::get('/bills/export', [LandlordBillController::class, 'export'])->name('bills.export');

    Route::get('/bills', [LandlordBillController::class, 'index'])->name('bills.index');
    Route::get('/bills/{bill}', [LandlordBillController::class, 'show'])->name('bills.show');
    Route::get('/bills/exportproperty/{month}', [LandlordBillController::class, 'exportAllBills'])->name('bills.exportproperty');
    // Nhập hoá đơn của chủ trọ
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [BillController::class, 'list'])->name('list');
        Route::get('/list', [BillController::class, 'index'])->name('index');
        Route::post('/{room}', [BillController::class, 'store'])->name('store');
        Route::get('/{room}/export', [BillController::class, 'exportExcel'])->name('exportExcel');
        Route::post('/room-bills/{id}/update-status', [BillController::class, 'updateStatus']);
    });

    // Staff yêu cầu chỉnh sửa phòng
    Route::prefix('staff/rooms')->name('staff.rooms.')->group(function () {
        Route::get('/{room}/edit', [StaffRoomEditController::class, 'edit'])->name('edit');
        Route::post('/{room}/request-update', [StaffRoomEditController::class, 'submitRequest'])->name('request_update');
    });

    Route::prefix('room-edit-requests')->name('room_edit_requests.')->group(function () {
        Route::get('/', [RoomEditRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [RoomEditRequestController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [RoomEditRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [RoomEditRequestController::class, 'reject'])->name('reject');
    });

    // Đánh dấu thông báo đã đọc
    Route::post('/staff/notifications/mark-as-read', function () {
        $user = auth()->user();

        $user->customNotifications()
            ->wherePivot('is_read', false)
            ->updateExistingPivot(
                $user->customNotifications()->pluck('notifications.id')->toArray(),
                ['is_read' => true, 'read_at' => now()]
            );
        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    })->name('staff.notifications.markAsRead');
});


Route::post('/staff/notifications/mark-as-read', function () {
    $user = auth()->user();

    $user->customNotifications()
        ->wherePivot('is_read', false)
        ->updateExistingPivot(
            $user->customNotifications()->pluck('notifications.id')->toArray(),
            ['is_read' => true, 'read_at' => now()]
        );
    return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
})->name('staff.notifications.markAsRead');


// Các route ngoài landlords


// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
});

// Auth routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/register', [AuthUserController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthUserController::class, 'register'])->name('register.post');
    Route::get('/login', [AuthUserController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthUserController::class, 'login'])->name('login.post');
    Route::any('/logout', [AuthUserController::class, 'logout'])->name('logout');
});

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('password/change', [ChangePasswordController::class, 'showChangeForm'])->name('password.change');

// Xử lý đổi mật khẩu
Route::post('password/change', [ChangePasswordController::class, 'updatePassword'])->name('password.change.update');

// Trang chủ
Route::get('/', [HomeController::class, 'renter'])->name('renter');
Route::get('/status-agreement', [HomeController::class, 'StausAgreement'])->name('status.agreement');

// Thêm người dùng vào phòng (renter)
Route::prefix('room-users')->name('room-users.')->group(function () {
    Route::post('/create-user', [HomeController::class, 'create'])->name('create');
    Route::post('/store-user', [HomeController::class, 'store'])->name('store');

    // 👉 Form dừng thuê
    Route::get('/{room_id}/stop', [HomeController::class, 'stopRentForm'])->name('stopRentForm');

    // 👉 Xử lý POST dừng thuê
    Route::post('/{id}/stop', [HomeController::class, 'stopUserRental'])->name('stop');
});



// Profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [TenantProfileController::class, 'edit'])->name('tenant.profile.edit');
    Route::put('/profile/update', [TenantProfileController::class, 'update'])->name('tenant.profile.update');
    Route::put('/profile/avatar', [TenantProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::get('/favorites', [HomeController::class, 'favorites'])->name('home.favorites');
    Route::post('/favorites/{property}', [HomeController::class, 'toggleFavorite'])->name('home.favorites.toggle');
    Route::post('/my-room/confirm-payment/{bill}', [MyRoomController::class, 'confirmPayment'])->name('home.my-room.confirm-payment');
    Route::get('/my-room', [MyRoomController::class, 'index'])->name('my-room');
    Route::post('/my-room/renew/{room}', [MyRoomController::class, 'renew'])->name('client.contract.renew');
    Route::post('/bills/{bill}/mark-pending', [RoomBillController::class, 'markPending'])->name('bills.markPending');
});
// Routes cho user bookings
Route::prefix('user')->middleware(['auth'])->group(function () {
    Route::get('/bookings', [UserBookingController::class, 'index'])
        ->name('user.bookings.index');
    Route::get('/bookings/{id}', [UserBookingController::class, 'show'])
        ->name('user.bookings.show');
});


// Admin profile
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
});

// Tái Ký Hợp Đồng
Route::middleware(['auth'])->prefix('staff/contract')->name('staff.contract.')->group(function () {
    Route::get('/renewals', [ContractRenewalController::class, 'index'])->name('renewals.index');
});
// Renter thêm người
Route::middleware('auth')->group(function () {
    Route::get('/add-user', [AddUserRequestController::class, 'create'])->name('renter.addUserRequest.create');
    Route::post('/add-user', [AddUserRequestController::class, 'store'])->name('renter.storeuser');
    Route::post('/parse-cccd', [AddUserRequestController::class, 'parseCCCD'])->name('renter.parseCCCD');
});

Route::middleware(['auth'])->group(function () {
    /**
     * ====================================
     * 👤 Renter (Người thuê) Complaints
     * ====================================
     */
    Route::prefix('complaints')->name('home.complaints.')->group(function () {
        Route::get('/', [RenterComplaintController::class, 'index'])->name('index');
        Route::get('/create', [RenterComplaintController::class, 'create'])->name('create');
        Route::post('/', [RenterComplaintController::class, 'store'])->name('store');
        Route::get('/{complaint}', [RenterComplaintController::class, 'show'])->name('show');
        Route::get('/{complaint}/edit', [RenterComplaintController::class, 'edit'])->name('edit');
        Route::put('/{complaint}', [RenterComplaintController::class, 'update'])->name('update');
        Route::patch('/{complaint}', [RenterComplaintController::class, 'update']); // optional
        Route::delete('/{complaint}', [RenterComplaintController::class, 'destroy'])->name('destroy');
        Route::post('/{complaint}/cancel', [RenterComplaintController::class, 'cancel'])->name('cancel');
    });

    /**
     * ====================================
     * 🧑‍💼 Landlord (Chủ nhà)
     * ====================================
     */
    Route::prefix('landlord')->name('landlord.')->group(function () {
        // Khiếu nại
        Route::get('/complaints', [LandlordComplaintController::class, 'index'])->name('complaints.index');
Route::get('/complaints/{id}', [LandlordComplaintController::class, 'show'])->name('complaints.show');
Route::post('/complaints/{id}/approve', [LandlordComplaintController::class, 'approve'])->name('complaints.approve');
Route::get('/complaints/{id}/rejection', [LandlordComplaintController::class, 'showRejection'])->name('complaints.rejection');
Route::get('/complaints/{id}/assign', [LandLordComplaintController::class, 'assignForm'])->name('complaints.assign.form');
Route::post('/complaints/{id}/assign', [LandLordComplaintController::class, 'assign'])->name('complaints.assign');
Route::post('/complaints/{id}/accept-reject', [LandLordComplaintController::class, 'acceptReject'])->name('complaints.accept-reject');
Route::get('/complaints/{id}/resolve', [LandLordComplaintController::class, 'showResolveForm'])->name('complaints.resolve.form');
Route::post('/complaints/{id}/resolve', [LandLordComplaintController::class, 'resolveAsLandlord'])->name('complaints.resolve');
Route::get('/complaints/{id}/reject', [LandLordComplaintController::class, 'rejectAsLandlordForm'])->name('complaints.reject.form');
Route::post('/complaints/{id}/reject', [LandLordComplaintController::class, 'rejectAsLandlord'])->name('complaints.reject');
        // Thông báo
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [LandLordNotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [LandLordNotificationController::class, 'markAsRead'])->name('read');
            Route::delete('/{id}', [LandLordNotificationController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [LandLordNotificationController::class, 'bulkDelete'])->name('bulk-delete');
        });
    });

    /**
     * ====================================
     * 🧑‍🔧 Staff (Nhân viên)
     * ====================================
     */
    Route::prefix('staff')->name('landlord.staff.')->group(function () {
        Route::get('/complaints', [StaffComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/history', [StaffComplaintController::class, 'history'])->name('complaints.history');
        Route::get('/complaints/{id}/edit', [StaffComplaintController::class, 'edit'])->name('complaints.edit');
        Route::post('/complaints/{id}/resolve', [StaffComplaintController::class, 'resolve'])->name('complaints.resolve');
        Route::get('/complaints/{id}/reject', [StaffComplaintController::class, 'rejectForm'])->name('complaints.rejectform');
        Route::post('/complaints/{id}/reject', [StaffComplaintController::class, 'reject'])->name('complaints.reject');

        Route::get('/complaints/{id}', [StaffComplaintController::class, 'show'])->name('complaints.show');
        Route::delete('/complaints/{id}', [StaffComplaintController::class, 'destroy'])->name('complaints.destroy');


        Route::get('/complaints/{id}', [StaffComplaintController::class, 'show'])->name('complaints.show');
        Route::delete('/complaints/{id}', [StaffComplaintController::class, 'destroy'])->name('complaints.destroy');



        Route::get('/complaints/{id}', [StaffComplaintController::class, 'show'])->name('complaints.show');
        Route::delete('/complaints/{id}', [StaffComplaintController::class, 'destroy'])->name('complaints.destroy');



        Route::get('/complaints/{id}', [StaffComplaintController::class, 'show'])->name('complaints.show');
        Route::delete('/complaints/{id}', [StaffComplaintController::class, 'destroy'])->name('complaints.destroy');
        Route::get('/chart', [ChartController::class, 'index'])->name('chart.index');
        Route::get('/complaint', [ChartController::class, 'complaintChart'])->name('landlord.staff.chart.complaint');
        Route::get('/booking', [ChartController::class, 'bookingChart'])->name('landlord.staff.chart.booking');
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [StaffNotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [StaffNotificationController::class, 'markAsRead'])->name('read');
        });
    });

    /**
     * ====================================
     * 🔔 Renter Notifications
     * ====================================
     */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [RenterNotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [RenterNotificationController::class, 'markAsRead'])->name('read');
        Route::delete('/{id}', [RenterNotificationController::class, 'destroy'])->name('delete');
        Route::post('/bulk-delete', [RenterNotificationController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/mark-all-read', [StaffNotificationController::class, 'markAllAsRead'])->name('markAllRead');
    });
    // Renter yêu cầu rời phòng

    Route::prefix('room-leave')->middleware(['auth'])->group(function () {
        Route::get('/list', [RoomLeaveController::class, 'index'])->name('home.roomleave.stopRentForm');
        Route::post('/request', [RoomLeaveController::class, 'sendLeaveRequest'])->name('home.roomleave.send');
        Route::get('/{id}/view', [RoomLeaveController::class, 'viewRequest'])->name('home.roomleave.viewRequest');
        Route::delete('/{id}/cancel', [RoomLeaveController::class, 'cancelRequest'])->name('home.roomleave.cancelRequest');
        Route::post('/home/roomleave/{id}/finalize', [RoomLeaveController::class, 'finalize'])->name('home.roomleave.finalize');
        Route::post('/transfer/accept', [RoomLeaveController::class, 'acceptTransfer'])->name('renter.transfer.accept');
        Route::get('/transfer/confirm', [RoomLeaveController::class, 'confirmTransfer'])->name('roomleave.confirmTransfer');
        Route::get('/transfer/deposits', [RoomLeaveController::class, 'depositHistory'])->name('home.roomleave.deposits');
        Route::post('/transfer/reject/{id}', [RoomLeaveController::class, 'rejectTransfer'])
            ->name('renter.transfer.reject');
    });
    // Staff xử lý yêu cầu rời phòng
     Route::prefix('staff')->name('landlord.staff.')->group(function () {
        Route::get('/roomleave', [StaffRoomLeaveController::class, 'index'])->name('roomleave.index');
        Route::post('/roomleave/{id}/approve', [StaffRoomLeaveController::class, 'approve'])->name('roomleave.approve');
        Route::get('/roomleave/{id}/show', [StaffRoomLeaveController::class, 'show'])->name('roomleave.show');
        Route::post('/roomleave/{id}/finalize', [StaffRoomLeaveController::class, 'finalize'])->name('roomleave.finalize');
        Route::get('/roomleave/processed', [StaffRoomLeaveController::class, 'processed'])->name('roomleave.processed');
    });
  
    Route::prefix('landlord')->name('landlord.')->group(function () {

        Route::get('/roomleave', [LandlordRoomLeaveController::class, 'index'])->name('roomleave.index');
        Route::get('/{id}/show', [LandlordRoomLeaveController::class, 'show'])->name('roomleave.show');
        Route::post('/{id}/approve', [LandlordRoomLeaveController::class, 'approve'])->name('roomleave.approve');
        Route::get('/{id}/reject-form', [LandlordRoomLeaveController::class, 'rejectForm'])->name('roomleave.rejectForm');
        Route::post('/{id}/reject', [LandlordRoomLeaveController::class, 'reject'])->name('roomleave.reject');
        Route::get('roomleave/processed', [LandlordRoomLeaveController::class, 'processed'])->name('roomleave.processed');
        Route::post('/roomleave/{id}/transfer-submit', [LandlordRoomLeaveController::class, 'submitTransferForm'])
            ->name('roomleave.transfer.submit');
        Route::get('/roomleave/accept/{id}', [LandlordRoomLeaveController::class, 'acceptTransfer'])
            ->name('roomleave.accept');
    });
});
Route::middleware(['auth'])->group(function () {
    // Trang thống kê người thuê
    Route::get('/dashboard-renter', [DashboardRenterController::class, 'index'])->name('home.profile.tenants.dashboard');

    // So sánh chi phí giữa 2 mốc thời gian
    Route::get('/compare-room-bills', [DashboardRenterController::class, 'compare'])->name('home.profile.tenants.compare');
});




Route::post('/bookings', [BookingController::class, 'store'])->name('bookings');
Route::middleware(['auth'])->prefix('staff/posts')->name('staff.posts.')->group(function () {
    Route::get('/', [StaffPostController::class, 'index'])->name('index');
    Route::get('/create', [StaffPostController::class, 'create'])->name('create');
    Route::post('/', [StaffPostController::class, 'store'])->name('store');


    // 🔧 Sửa lại ở đây
    Route::get('/{post}', [StaffPostController::class, 'show'])->name('show');
    Route::get('/{post}/edit', [StaffPostController::class, 'edit'])->name('edit');
    Route::put('/{post}', [StaffPostController::class, 'update'])->name('update');
    Route::delete('/{post}', [StaffPostController::class, 'destroy'])->name('destroy');
});

Route::prefix('landlords')->middleware(['auth'])->group(function () {
    Route::get('/posts/approval', [PostApprovalController::class, 'index'])->name('landlord.posts.approval.index');
    Route::post('/posts/approval/{post}/approve', [PostApprovalController::class, 'approve'])->name('landlord.posts.approval.approve');
    Route::post('/posts/approval/{post}/reject', [PostApprovalController::class, 'reject'])->name('landlord.posts.approval.reject');
    Route::get('/posts/approval/{post}', [PostApprovalController::class, 'show'])
        ->name('landlord.posts.approval.show');
    Route::post('/posts/{post}/hide', [PostApprovalController::class, 'hide'])->name('landlord.posts.approval.hide');
    Route::post('/posts/{post}/unhide', [PostApprovalController::class, 'unhide'])->name('landlord.posts.approval.unhide');
});


// routes/web.php hoặc routes/landlord.php nếu chia subdomain
Route::prefix('staff')->name('staff.')->group(function () {
    Route::resource('categories', \App\Http\Controllers\Landlord\Staff\CategoryController::class);
});

Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
// routes/web.php
Route::post('/posts/suggest-nearby', [PostController::class, 'suggestNearby'])
    ->name('posts.suggestNearby')
    ->middleware('throttle:10,1'); // Giới hạn 10 requests/phút

Route::get('/get-rooms/{property_id}', [RoomController::class, 'getRoomsByProperty']);

Route::prefix('landlord/bookings')->middleware(['auth'])->name('landlord.bookings.')->group(function () {
    Route::get('/', [BookingsController::class, 'index'])->name('index');
    Route::post('/{booking}/approve', [BookingsController::class, 'approve'])->name('approve');
    Route::post('/{booking}/reject', [BookingsController::class, 'reject'])->name('reject');
    Route::post('/{booking}/waiting', [BookingsController::class, 'waiting'])->name('waiting');
    Route::post('/{booking}/completed', [BookingsController::class, 'completed'])->name('completed');
    Route::post('/{booking}/no-cancel', [BookingsController::class, 'noCancel'])->name('noCancel');
    Route::post('/{booking}/completed-with-image', [BookingsController::class, 'doneWithImage'])->name('completedWithImage');
});

Route::get('/tenants/history', [RenterHistoryBillController::class, 'index'])->name('home.profile.tenants.history');

Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/staff_booking', [StaffBookingController::class, 'index'])->name('booking.index');
Route::prefix('staff/bookings')->group(function () {
    Route::post('{id}/wait', [StaffBookingController::class, 'wait']);
    Route::post('{id}/done', [StaffBookingController::class, 'done']);
    Route::post('{id}/no-cancel', [StaffBookingController::class, 'noShow']);
    Route::post('{id}/done-with-image', [StaffBookingController::class, 'doneWithImage']);
});

Route::middleware(['auth', 'role:Landlord'])->prefix('landlord')->group(function () {
    Route::get('/posts', [App\Http\Controllers\Landlord\PostController::class, 'index'])->name('landlord.posts.index');
    Route::get('/posts/create', [App\Http\Controllers\Landlord\PostController::class, 'create'])->name('landlord.posts.create');
    Route::post('/posts', [App\Http\Controllers\Landlord\PostController::class, 'store'])->name('landlord.posts.store');
    Route::delete('/posts/{id}', [App\Http\Controllers\Landlord\PostController::class, 'destroy'])->name('landlord.posts.destroy');
    Route::get('/posts/{id}', [App\Http\Controllers\Landlord\PostController::class, 'show'])->name('landlord.posts.show');
});


Route::prefix('search')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/search', [HomeController::class, 'search'])->name('search.results');
    Route::get('/search/api-suggestions', [HomeController::class, 'apiSuggestions'])->name('search.api-suggestions');
});
// Trong routes/web.php hoặc routes/api.php
Route::get('/debug-api-structure', [HomeController::class, 'debugApiStructure']);
