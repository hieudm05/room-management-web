<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }

        thead th {
            background-color: #99FF00;
        }

        tbody tr {
            background-color: #FFFFCC;
        }

        .highlight {
            background-color: #FFFF00;
        }

        .orange-light {
            background-color: #FFD580;
        }

        .pink-light {
            background-color: #FFB6C1;
        }

        .yellow-light {
            background-color: #FFFF99;
        }
    </style>
</head>

<body>
    <h2>QUẢN LÝ TIỀN ĐIỆN/ NƯỚC PHÒNG TRỌ - THÁNG {{ $data['month'] }}</h2>
    <table>
        <thead>
            <tr>
                <th rowspan="2">Phòng</th>
                <th rowspan="2">Họ tên</th>
                <th colspan="2">Số công tơ điện</th>
                <th rowspan="2">Số điện tiêu thụ (kWh)</th>
                <th rowspan="2">Tiền điện</th>
                <th rowspan="2">Số nước
                    <br>(@if ($data['water_unit'] === 'per_person')
                        Người
                    @elseif($data['water_unit'] === 'per_m3')
                        m³
                    @else
                        {{ $data['water_unit'] }}
                    @endif)
                </th>
                <th rowspan="2">Tiền nước</th>
                <th rowspan="2" class="highlight">Tiền phòng</th>
                <th rowspan="2">Dịch vụ</th>
                <th rowspan="2" class="highlight">Tổng cộng</th>
            </tr>
            <tr>
                <th>Chỉ số đầu</th>
                <th>Chỉ số cuối</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $data['room_name'] }}</td>
                <td>{{ $data['tenant_name'] }}</td>
                <td class="orange-light">{{ $data['electric_start'] }}</td>
                <td class="orange-light">{{ $data['electric_end'] }}</td>
                <td>{{ $data['electric_kwh'] }}</td>
                <td class="yellow-light">{{ number_format($data['electric_total'], 0, ',', '.') }} VND</td>
                <td class="pink-light">
                    @if ($data['water_unit'] === 'per_person')
                        {{ $data['water_occupants'] }} người
                    @elseif($data['water_unit'] === 'per_m3')
                        {{ $data['water_m3'] }} m³
                    @else
                        -
                    @endif
                </td>
                <td class="yellow-light">{{ number_format($data['water_total'], 0, ',', '.') }} VND</td>
                <td class="highlight yellow-light">{{ number_format($data['rent_price'], 0, ',', '.') }}</td>
                <td class="yellow-light">
                    @if (count($data['services']) > 0)
                        @foreach ($data['services'] as $sv)
                            {{ $sv['name'] }}: {{ number_format($sv['total'], 0, ',', '.') }} <br>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td class="highlight yellow-light">{{ number_format($data['total'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
