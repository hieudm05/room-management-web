<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\LandLordComplaintController;
use App\Http\Controllers\Renter\RenterComplaintController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\AuthLandlordController;
use App\Http\Controllers\Client\AuthUserController;
use App\Http\Controllers\Client\ForgotPasswordController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ResetPasswordController;
use App\Http\Controllers\Landlord\ApprovalController;
use App\Http\Controllers\Landlord\ApprovalUserController;
use App\Http\Controllers\Landlord\BankAccountController;
use App\Http\Controllers\Landlord\LandlordBankAccountController;
use App\Http\Controllers\Landlord\PropertyBankAccountController;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\Landlord\PropertyRoomBankAccountController;
use App\Http\Controllers\Landlord\RoomController;
use App\Http\Controllers\Landlord\RoomEditRequestController;

use App\Http\Controllers\Landlord\Staff\ContractController;
use App\Http\Controllers\Landlord\Staff\DocumentController;
use App\Http\Controllers\Landlord\Staff\ElectricWaterController;
use App\Http\Controllers\Landlord\Staff\PaymentController;
use App\Http\Controllers\Landlord\Staff\ServiceController;
use App\Http\Controllers\Landlord\Staff\StaffRoomController;
use App\Http\Controllers\Landlord\Staff\StaffRoomEditController;
use App\Http\Controllers\Renter\AddUserRequestController;
use App\Http\Controllers\TenantProfileController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\Client\MyRoomController;
use App\Http\Controllers\Landlord\LandlordBillController;
use App\Http\Controllers\RoomBillController;
use App\Http\Controllers\Landlord\OCRController;
use App\Http\Controllers\Landlord\StaffAccountController;
// Địa chỉ
use App\Http\Controllers\Landlord\RoomStaffController;
use App\Http\Controllers\Landlord\Staff\StaffComplaintController;
use App\Http\Controllers\Renter\RenterNotificationController;
use App\Http\Controllers\Landlord\landLordNotificationController;
use App\Http\Controllers\Landlord\Staff\StaffNotificationController;
Route::get('/provinces', [AddressController::class, 'getProvinces']);
Route::get('/districts/{provinceCode}', [AddressController::class, 'getDistricts']);
Route::get('/wards/{districtCode}', [AddressController::class, 'getWards']);

// Auth chung
Route::get('/login', [AuthUserController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthUserController::class, 'login'])->name('login.post');

// LANDLORD
Route::prefix('landlords')->name('landlords.')->middleware(['auth'])->group(function () {

    Route::get('/', fn() => view('landlord.dashboard'))->name('dashboard');

    Route::get('/register', [AuthLandlordController::class, 'showForm'])->name('register.form');
    Route::post('/register', [AuthLandlordController::class, 'submit'])->name('register.submit');
// Duyệt hợp đồng
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::delete('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    // Duyệt thêm người
    Route::get('/approvals/users', [ApprovalUserController::class, 'index'])->name('approvals.users.index');
    Route::post('/approvals/users/{id}/approve', [ApprovalUserController::class, 'approveUser'])->name('approvals.users.approve');
    Route::delete('/approvals/users/{id}/reject', [ApprovalUserController::class, 'reject'])->name('approvals.users.reject');

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

    // Rooms
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/store', [RoomController::class, 'store'])->name('store');
        Route::post('/{room}/lock', [RoomController::class, 'lockContract'])->name('lockContract');
        Route::get('/{room}/stats', [RoomController::class, 'showStats'])->name('stats');
        Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{room}', [RoomController::class, 'update'])->name('update');
        Route::put('/{room}/hide', [RoomController::class, 'hide'])->name('hide');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
        Route::get('/{room}', [RoomController::class, 'show'])->name('show');
        Route::get('/{room}/contract-pdf', [RoomController::class, 'streamContract'])->name('contract.pdf');
        Route::get('/{room}/contract-download', [RoomController::class, 'downloadContract'])->name('contract.download');
        Route::get('/{room}/contract-word', [RoomController::class, 'downloadContractWord'])->name('contract.word');
        Route::get('/{room}/contract-form', [RoomController::class, 'formShowContract'])->name('contract.info');
        Route::post('/{room}/contract-confirm-rentalAgreement', [RoomController::class, 'confirmStatusrentalAgreement'])->name('contract.confirmLG');
        Route::post('/room-users/{id}/suscess', [RoomController::class, 'ConfirmAllUser'])->name('room_users.suscess');
        Route::get('/{room}/staffs', [RoomStaffController::class, 'edit'])->name('staffs.edit');
        Route::post('/{room}/staffs', [RoomStaffController::class, 'update'])->name('staffs.update');
    });

    // Staff quản lý phòng
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffRoomController::class, 'index'])->name('index');
        Route::get('/{room}/show', [StaffRoomController::class, 'show'])->name('show');

        Route::prefix('contract')->name('contract.')->group(function () {
Route::get('/{room}', [ContractController::class, 'index']);
            Route::post('/{room}/upload', [ContractController::class, 'uploadAgreementFile'])->name('upload');
        });

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
            // Route::get('/{room}', [PaymentController::class, 'index']);
            // Route::post('/{room}/store', [PaymentController::class, 'store'])->name('store');
            // Route::get('/{room}/export-excel', [PaymentController::class, 'exportExcel'])->name('export');

            // Route::get('api/payment/{room}', [PaymentController::class, 'getBillByMonth'])->name('payment.api');
            // Route::post('/{room}/send-bill', action: [PaymentController::class, 'sendBillmmm'])->name('payment.send_bills');

            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::post('/{room}', [PaymentController::class, 'store'])->name('store');
            Route::get('/{room}/export', [PaymentController::class, 'exportExcel'])->name('exportExcel');
            Route::post('/room-bills/{id}/update-status', [PaymentController::class, 'updateStatus']);

        });

       
    });
     // Bill của chủ trọ
         Route::get('/bills', [LandlordBillController::class, 'index'])->name('bills.index'); 
        Route::get('/bills/{bill}', [LandlordBillController::class, 'show'])->name('bills.show');
        Route::get('/bills/export', [LandlordBillController::class, 'export'])->name('bills.export');

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

// Các route ngoài landlords
Route::prefix('rooms')->group(function () {
    Route::post('/{room}/contracts/preview', [RoomController::class, 'previewContract'])->name('contracts.preview');
    Route::post('/{room}/contracts/confirm', [RoomController::class, 'confirmContract'])->name('contracts.confirm');
    Route::get('/{room}', [RoomController::class, 'show2'])->name('show2');
});

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

// Trang chủ
Route::get('/', [HomeController::class, 'renter'])->name('renter');
Route::get('/status-agreement', [HomeController::class, 'StausAgreement'])->name('status.agreement');

// Thêm người dùng vào phòng (renter)
Route::prefix('room-users')->name('room-users.')->group(function () {
    Route::post('/create-user', [HomeController::class, 'create'])->name('create');
    Route::post('/store-user', [HomeController::class, 'store'])->name('store');
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
    Route::post('/bills/{bill}/mark-pending', [RoomBillController::class, 'markPending'])->name('bills.markPending');
});

// Admin profile
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
});

// Renter thêm người
Route::middleware('auth')->group(function () {
    Route::get('/add-user', [AddUserRequestController::class, 'create'])->name('renter.addUserRequest.create');
    Route::post('/add-user', [AddUserRequestController::class, 'store'])->name('renter.storeuser');
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
    Route::prefix('staff')->name('landlords.staff.')->group(function () {
        Route::get('/complaints', [StaffComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/{id}/edit', [StaffComplaintController::class, 'edit'])->name('complaints.edit');
        Route::post('/complaints/{id}/resolve', [StaffComplaintController::class, 'resolve'])->name('complaints.resolve');
        Route::get('/complaints/{id}/reject', [StaffComplaintController::class, 'rejectForm'])->name('complaints.rejectform');
        Route::post('/complaints/{id}/reject', [StaffComplaintController::class, 'reject'])->name('complaints.reject');

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
});
