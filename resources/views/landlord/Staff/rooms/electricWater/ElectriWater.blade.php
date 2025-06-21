@extends('landlord.layouts.app')

@section('title', 'Tiền điện nước')

@section('content')

    <div class="container my-4">
        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif
        <h3 class="mb-4">📄 Tính tiền điện nước</h3>

        @php
            $occupants = $room->occupants ?? 0;

            // Tìm dịch vụ Điện (service_id = 1)
            $electricService = $room->services->firstWhere('service_id', 1);
            $electricDescription = '';
            if ($electricService) {
                $unit = $electricService->pivot->unit;
                $eletricPrice = $electricService->pivot->price ?? 0;
                $customDescription = $electricService->pivot->description ?? null;

                if ($customDescription) {
                    // Nếu có mô tả riêng, hiển thị mô tả đó
                    $electricDescription = $customDescription;
                } elseif ($unit === 'per_person') {
                    $total = $occupants * $eletricPrice;
                    $electricDescription =
                        'Tính theo đầu người - Tổng: ' .
                        number_format($total) .
                        ' VNĐ (' .
                        $occupants .
                        ' người x ' .
                        number_format($eletricPrice) .
                        ' VNĐ)';
                } elseif ($unit === 'per_room') {
                    $electricDescription = 'Tính theo phòng (giá cố định): ' . number_format($eletricPrice) . ' VNĐ';
                } else {
                    $electricDescription = 'Giá: ' . number_format($eletricPrice) . ' VNĐ/kWh';
                }
            }

            // Tìm dịch vụ Nước (service_id = 2)
            $waterService = $room->services->firstWhere('service_id', 2);
            $waterDescription = '';
            if ($waterService) {
                $unit = $waterService->pivot->unit;
                $price = $waterService->pivot->price ?? 0;

                if ($unit === 'per_person') {
                    $total = $occupants * $price;
                    $waterDescription =
                        'Tính theo đầu người  ' .
                        $occupants .
                        ' người (' .
                        ' mỗi người / ' .
                        number_format($price) .
                        ' VNĐ)';
                } elseif ($unit === 'per_m3') {
                    $waterDescription = 'Tính theo khối: ' . number_format($price) . ' VNĐ / m³';
                } else {
                    $waterDescription = 'Giá: ' . number_format($price) . ' VNĐ';
                }
            }
        @endphp


        <form action="{{ route('landlords.staff.electric_water.store', $room->room_id) }}" method="post"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->room_id }}">
            <div class="mb-3">
                <label class="form-label">Chọn khoảng thời gian:</label>
                <div class="row">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Từ ngày</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">Đến ngày</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Nhập số điện của bạn:</label>
                <div class="row">
                    <div class="col-md-6">
                        <label for="electric_start" class="form-label">Chỉ số điện đầu (kWh)</label>
                        <input type="number" class="form-control" name="electric_start" id="electric_start"
                            placeholder="VD: 1234">
                    </div>
                    <div class="col-md-6">
                        <label for="electric_end" class="form-label">Chỉ số điện cuối (kWh)</label>
                        <input type="number" class="form-control" name="electric_end" id="electric_end"
                            placeholder="VD: 1300">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Tiền Điện</label><br>
                @if ($electricDescription)
                    <small class="text-muted">{{ $electricDescription }}</small>
                @endif
                <input type="number" class="form-control mt-1" name="kwh" id="kwh_input"
                    placeholder="Nhập số điện đã dùng (kWh)" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Thành tiền điện (VNĐ)</label>
                <input type="text" class="form-control" id="electric_total" name="electricity" readonly>
                <input type="hidden" id="electric_price" value="{{ $eletricPrice }}">
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Tiền Nước</label><br>
                @if ($waterDescription)
                    <small class="text-muted">{{ $waterDescription }}</small>
                @endif

                {{-- Trường hợp tính theo khối --}}
                <div id="water_by_m3_group" class="mt-1">
                    <input type="number" class="form-control" name="water_m3" id="water_input"
                        placeholder="Nhập số m³ đã dùng">
                </div>

                {{-- Trường hợp tính theo người --}}
                <div id="water_by_person_group" class="mt-1" style="display: none;">
                    <input type="text" class="form-control" value="{{ $occupants }} người" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Thành tiền nước (VNĐ)</label>
                <input type="text" class="form-control" id="water_total" name="water" readonly>
                <input type="hidden" id="water_price" value="{{ $price }}">
                <input type="hidden" id="water_unit" name="water_unit" value="{{ $unit }}">
                <input type="hidden" id="water_occupants" name="water_occupants" value="{{ $occupants }}">
            </div>

            <div class="mb-3">
                <label for="images" class="form-label">Tải ảnh lên</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                <div id="preview" class="row g-3 mt-2"></div>
            </div>



            <button type="submit" class="btn btn-primary">Xác nhận</button>
        </form>
    </div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const electricStart = document.getElementById("electric_start");
        const electricEnd = document.getElementById("electric_end");
        const kwhInput = document.getElementById("kwh_input");
        const totalOutput = document.getElementById("electric_total");
        const price = parseFloat(document.getElementById("electric_price").value);

        function updateFromStartEnd() {
            const start = parseFloat(electricStart.value) || 0;
            const end = parseFloat(electricEnd.value) || 0;
            if (end >= start) {
                const used = end - start;
                kwhInput.value = used;
                totalOutput.value = new Intl.NumberFormat('vi-VN').format(used * price) + ' VNĐ';
            } else {
                totalOutput.value = "⚠️ Chỉ số cuối phải lớn hơn đầu";
            }
        }

        function updateFromKwh() {
            const start = parseFloat(electricStart.value) || 0;
            const used = parseFloat(kwhInput.value) || 0;
            const end = start + used;
            electricEnd.value = end;
            totalOutput.value = new Intl.NumberFormat('vi-VN').format(used * price) + ' VNĐ';
        }

        electricStart.addEventListener("input", () => {
            updateFromStartEnd();
        });

        electricEnd.addEventListener("input", () => {
            updateFromStartEnd();
        });

        kwhInput.addEventListener("input", () => {
            updateFromKwh();
        });
    });


    document.addEventListener("DOMContentLoaded", function() {
        const waterTotalOutput = document.getElementById("water_total");
        const waterPrice = parseFloat(document.getElementById("water_price").value);
        const waterUnit = document.getElementById("water_unit").value;
        const waterOccupants = parseInt(document.getElementById("water_occupants").value);
        const waterInput = document.getElementById("water_input");
        const byM3Group = document.getElementById("water_by_m3_group");
        const byPersonGroup = document.getElementById("water_by_person_group");

        if (waterUnit === "per_person") {
            byM3Group.style.display = "none";
            byPersonGroup.style.display = "block";
            const total = waterPrice * waterOccupants;
            waterTotalOutput.value = new Intl.NumberFormat('vi-VN').format(total) + " VNĐ";
        } else {
            byM3Group.style.display = "block";
            byPersonGroup.style.display = "none";

            waterInput.addEventListener("input", function() {
                const m3 = parseFloat(waterInput.value) || 0;
                const total = m3 * waterPrice;
                waterTotalOutput.value = new Intl.NumberFormat('vi-VN').format(total) + " VNĐ";
            });
        }
    });


    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById("images");
        const preview = document.getElementById("preview");

        input.addEventListener("change", function() {
            preview.innerHTML = "";

            const files = input.files;

            Array.from(files).forEach(file => {
                if (!file.type.startsWith("image/")) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement("img");
                    const wrapper = document.createElement("div");
                    wrapper.classList.add("col-md-3");
                    wrapper.appendChild(img)
                    img.src = e.target.result;
                    img.classList.add("img-thumbnail");
                    img.style.maxWidth = "200px";
                    img.style.maxHeight = "100px";
                    preview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
