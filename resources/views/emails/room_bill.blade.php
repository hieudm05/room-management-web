{{-- filepath: resources/views/emails/room_bill.blade.php --}}
@component('mail::message')
# HÓA ĐƠN PHÒNG TRỌ THÁNG {{ $data['month'] }}

Xin chào **{{ $data['tenant_name'] }}**,

Bạn nhận được hóa đơn phòng trọ tháng **{{ $data['month'] }}** cho phòng **{{ $data['room_name'] }}**.

---

## **Thông tin thanh toán**

- **Tổng tiền cần thanh toán:** <span style="color: #d9534f; font-size: 18px;"><strong>{{ number_format($data['total']) }} VNĐ</strong></span>
- **Ngân hàng:** <strong>{{ $bankAccount['bank_name'] }}</strong>
- **Chủ tài khoản:** <strong>{{ $bankAccount['account_name'] }}</strong>
- **Số tài khoản:** <strong>{{ $bankAccount['account_no'] }}</strong>
- **Nội dung chuyển khoản:** <strong>{{ $bankAccount['transfer_content'] }}</strong>

---

## **Quét mã QR để chuyển khoản nhanh**
<p>Đây là hóa đơn tiền phòng tháng {{ \Carbon\Carbon::parse($data['month'])->format('m/Y') }}.</p>

> Bạn chỉ cần mở app ngân hàng, quét mã QR này. Thông tin chuyển khoản và số tiền sẽ tự động điền sẵn.

---

**Chi tiết hóa đơn được đính kèm trong file Excel. Vui lòng kiểm tra file đính kèm để xem chi tiết.**

---

Nếu có thắc mắc về hóa đơn, vui lòng liên hệ lại với chủ trọ.

Cảm ơn bạn đã sử dụng dịch vụ!

@endcomponent