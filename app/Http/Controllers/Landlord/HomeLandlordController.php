<?php

namespace App\Http\Controllers\LandLord;

use App\Http\Controllers\Controller;
use App\Models\Booking as ModelsBooking;
use App\Models\LandLord\Property;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HomeLandLordController extends Controller
{
    public function index()
    {
        $properties = Property::all();

        $total_rooms = Property::with('rooms')->get()->sum(function ($property) {
            return $property->rooms->count();
        });

        // SỬA: Tính total_rented dựa trên trạng thái hợp đồng Active
        $rented_room_ids = DB::table('rental_agreements')
            ->where('status', 'Active')
            ->distinct()
            ->pluck('room_id');
        
        $total_rented = $rented_room_ids->count();
        $total_empty = $total_rooms - $total_rented;

        $total_revenue = Property::with(['rooms.bills'])->get()->sum(function ($property) {
            $bills = $property->rooms->flatMap->bills;
            return $bills->sum('total');
        });

        $total_complaints = Property::with(['rooms.complaints'])->get()->sum(function ($property) {
            return $property->rooms->flatMap->complaints->count();
        });

        $total_bookings = 0;
        try {
            $total_bookings = ModelsBooking::count();
        } catch (\Exception $e) {
            Log::warning('Booking model not found, using default value');
            $total_bookings = 0;
        }

        $propertyStats = Property::with(['rooms', 'rooms.bills', 'rooms.complaints', 'rooms.bookings'])
            ->take(5)
            ->get()
            ->map(function ($property) {
                $rooms = $property->rooms;
                $total_rooms = $rooms->count();
                
                // SỬA: Sử dụng contracts với status Active để xác định phòng đã thuê
                $rented_room_ids = DB::table('rental_agreements')
                    ->whereIn('room_id', $rooms->pluck('id'))
                    ->where('status', 'Active')
                    ->distinct()
                    ->pluck('room_id');
                
                $rented_rooms = $rented_room_ids->count();
                $empty_rooms = $total_rooms - $rented_rooms;

                $bills = $rooms->flatMap->bills;
                $revenue = $bills->sum('total');
                $electric_cost = $bills->sum('electric_total');
                $water_cost = $bills->sum('water_total');
                $other_cost = $bills->sum('other_total');

                $complaints = $rooms->flatMap->complaints;
                $complaints_by_status = [
                    'pending' => $complaints->where('status', 'pending')->count(),
                    'in_progress' => $complaints->where('status', 'in_progress')->count(),
                    'resolved' => $complaints->where('status', 'resolved')->count(),
                    'reject' => $complaints->where('status', 'reject')->count(),
                ];

                $bookings = collect();
                try {
                    $bookings = $rooms->flatMap->bookings ?? collect();
                } catch (\Exception $e) {
                    Log::warning('Bookings relationship not found');
                }

                $bookings_by_status = [
                    'pending' => $bookings->where('status', 'pending')->count(),
                    'approved' => $bookings->where('status', 'approved')->count(),
                    'rejected' => $bookings->where('status', 'rejected')->count(),
                    'waiting' => $bookings->where('status', 'waiting')->count(),
                ];

                return [
                    'name' => $property->name,
                    'revenue' => $revenue ?? 0,
                    'total_rooms' => $total_rooms,
                    'rented_rooms' => $rented_rooms,
                    'empty_rooms' => $empty_rooms,
                    'electric_cost' => $electric_cost ?? 0,
                    'water_cost' => $water_cost ?? 0,
                    'other_cost' => $other_cost ?? 0,
                    'complaints' => $complaints->count(),
                    'complaints_by_status' => $complaints_by_status,
                    'bookings' => $bookings->count(),
                    'bookings_by_status' => $bookings_by_status,
                    'income' => $revenue ?? 0,
                    'expense' => ($electric_cost + $water_cost + $other_cost) ?? 0,
                ];
            })->values();

        $monthlyRevenue = collect(range(1, 12))->map(function ($month) {
            $bills = RoomBill::whereMonth('month', $month)
                ->whereYear('month', now()->year)
                ->get();
            return $bills->sum('total');
        });

        $revenueChartData = [
            'labels' => collect(range(1, 12))->map(fn($m) => 'Tháng ' . $m)->toArray(),
            'revenue' => $monthlyRevenue->toArray()
        ];

        $monthlyIncomeExpense = collect(range(1, 12))->map(function ($month) {
            $bills = RoomBill::whereMonth('month', $month)
                ->whereYear('month', now()->year)
                ->get();
            $income = $bills->sum('total');
            $expense = $bills->sum('electric_total') + $bills->sum('water_total') + $bills->sum('other_total');
            return [
                'income' => $income,
                'expense' => $expense
            ];
        });

        $incomeExpenseStats = [
            'labels' => collect(range(1, 12))->map(fn($m) => 'Tháng ' . $m)->toArray(),
            'income' => $monthlyIncomeExpense->pluck('income')->toArray(),
            'expense' => $monthlyIncomeExpense->pluck('expense')->toArray(),
        ];

        $monthlyOccupancy = $this->calculateOccupancyByContractStatus(now()->year);

        $occupancyChartData = [
            'labels' => collect(range(1, 12))->map(fn($m) => 'Tháng ' . $m)->toArray(),
            'occupancy' => $monthlyOccupancy->toArray()
        ];

        $complaintsStats = [
            'pending' => $propertyStats->sum('complaints_by_status.pending'),
            'in_progress' => $propertyStats->sum('complaints_by_status.in_progress'),
            'resolved' => $propertyStats->sum('complaints_by_status.resolved'),
            'reject' => $propertyStats->sum('complaints_by_status.reject'),
        ];

        $bookingsStats = [
            'pending' => $propertyStats->sum('bookings_by_status.pending'),
            'approved' => $propertyStats->sum('bookings_by_status.approved'),
            'rejected' => $propertyStats->sum('bookings_by_status.rejected'),
            'waiting' => $propertyStats->sum('bookings_by_status.waiting'),
        ];

        return view('landlord.dashboard', compact(
            'properties',
            'total_rooms',
            'total_rented',
            'total_empty',
            'total_revenue',
            'total_complaints',
            'total_bookings',
            'propertyStats',
            'incomeExpenseStats',
            'revenueChartData',
            'occupancyChartData',
            'complaintsStats',
            'bookingsStats'
        ));
    }

    // SỬA: Thay đổi tên method và logic tính toán dựa trên contract status
   // Cũng cần update method calculateOccupancyByContractStatus
private function calculateOccupancyByContractStatus($year, $selectedProperties = [])
{
    return collect(range(1, 12))->map(function ($month) use ($year, $selectedProperties) {
        // Đếm tổng phòng có sẵn đến cuối tháng đó
        $total_rooms_created = DB::table('rooms')
            ->join('properties', 'rooms.property_id', '=', 'properties.property_id')
            ->where('rooms.created_at', '<=', Carbon::create($year, $month)->endOfMonth())
            ->when(!empty($selectedProperties), function($q) use ($selectedProperties) {
                $q->whereIn('properties.name', $selectedProperties);
            })
            ->count();
            // dd($total_rooms_created);

        if ($total_rooms_created == 0) {
            return 0;
        }

        // Đếm hợp đồng được tạo trong tháng đó và vẫn Active
        $contracts_created_this_month = DB::table('rental_agreements')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'Active')
            ->distinct('room_id')
            ->count();

        $occupancy_rate = ($contracts_created_this_month / $total_rooms_created) * 100;
        return round($occupancy_rate, 2);
    });
}

    public function filterStats(Request $request)
    {
        Cache::forget('property_stats_' . md5(serialize($request->all())));
        $month = $request->input('month');
        $quarter = $request->input('quarter');
        $year = $request->input('year', now()->year);
        $year_from = $request->input('year_from');
        $year_to = $request->input('year_to', now()->year);
        $selected_properties = $request->input('properties', []);
        $compareA = $request->input('compareA');
        $compareB = $request->input('compareB');

        $cacheKey = 'property_stats_' . md5(serialize($request->all()));

        $data = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($month, $quarter, $year, $year_from, $year_to, $selected_properties, $compareA, $compareB) {
            $query = Property::with(['rooms', 'rooms.bills', 'rooms.complaints']);
            try {
                $query->with('rooms.bookings');
            } catch (\Exception $e) {
                Log::warning('Bookings relationship not available');
            }

            if (!empty($selected_properties)) {
                $query->whereIn('name', $selected_properties);
            } elseif ($compareA || $compareB) {
                $query->whereIn('name', array_filter([$compareA, $compareB]));
            } else {
                $query->take(5);
            }

            $properties = $query->get();

            if ($properties->isEmpty()) {
                Log::warning("No properties found for filter");
            }

            $propertyStats = $properties->map(function ($property) use ($month, $quarter, $year, $year_from, $year_to) {
                $rooms = $property->rooms;
                $total_rooms = $rooms->count();

                // SỬA: Tính rented_rooms và empty_rooms dựa trên contract status Active
                $contractQuery = DB::table('rental_agreements')
                    ->whereIn('room_id', $rooms->pluck('room_id'))
                    ->where('status', 'Active');

                // Áp dụng bộ lọc thời gian cho contracts
                if ($year_from && $year_to) {
                    $contractQuery->where(function($q) use ($year_from, $year_to) {
                        $q->whereBetween(DB::raw('YEAR(start_date)'), [$year_from, $year_to])
                          ->orWhere(function($q2) use ($year_from, $year_to) {
                              $q2->where(DB::raw('YEAR(start_date)'), '<', $year_from)
                                 ->where(DB::raw('YEAR(end_date)'), '>=', $year_from);
                          });
                    });
                } else {
                    if ($month) {
                        $contractQuery->where(function($q) use ($month) {
                            $q->where(DB::raw('DATE_FORMAT(start_date, "%Y-%m")'), '<=', $month)
                              ->where(function($q2) use ($month) {
                                  $q2->where(DB::raw('DATE_FORMAT(end_date, "%Y-%m")'), '>=', $month)
                                     ->orWhereNull('end_date');
                              });
                        });
                    }
                    if ($quarter && $year) {
                        $startMonth = ($quarter - 1) * 3 + 1;
                        $endMonth = $quarter * 3;
                        $contractQuery->where(function($q) use ($year, $startMonth, $endMonth) {
                            $q->where(DB::raw('YEAR(start_date)'), '<=', $year)
                              ->where(function($q2) use ($year, $startMonth, $endMonth) {
                                  $q2->where(function($q3) use ($year, $endMonth) {
                                      $q3->where(DB::raw('YEAR(end_date)'), '>', $year)
                                         ->orWhere(function($q4) use ($year, $endMonth) {
                                             $q4->where(DB::raw('YEAR(end_date)'), '=', $year)
                                                ->where(DB::raw('MONTH(end_date)'), '>=', $endMonth);
                                         });
                                  })
                                  ->orWhereNull('end_date');
                              });
                        });
                    }
                    if ($year && !$quarter && !$month) {
                        $contractQuery->where(function($q) use ($year) {
                            $q->where(DB::raw('YEAR(start_date)'), '<=', $year)
                              ->where(function($q2) use ($year) {
                                  $q2->where(DB::raw('YEAR(end_date)'), '>=', $year)
                                     ->orWhereNull('end_date');
                              });
                        });
                    }
                }

                $rented_room_ids = $contractQuery->distinct()->pluck('room_id');
                $rented_rooms = $rented_room_ids->count();
                $empty_rooms = $total_rooms - $rented_rooms;

                $bills = $rooms->flatMap->bills->filter(function ($bill) use ($month, $quarter, $year, $year_from, $year_to) {
                    $billMonth = substr($bill->month, 0, 7);
                    $billYear = (int)substr($bill->month, 0, 4);
                    $billQuarter = ceil((int)substr($bill->month, 5, 2) / 3);

                    if ($year_from && $year_to) {
                        return $billYear >= $year_from && $billYear <= $year_to;
                    }

                    return (
                        (!$month || $billMonth === $month) &&
                        (!$quarter || $billQuarter == $quarter) &&
                        (!$year || $billYear == $year)
                    );
                });

                $revenue = $bills->sum('total');
                $electric_cost = $bills->sum('electric_total');
                $water_cost = $bills->sum('water_total');
                $other_cost = $bills->sum('other_total');

                $complaints = $rooms->flatMap->complaints->filter(function ($complaint) use ($month, $quarter, $year, $year_from, $year_to) {
                    $createdYear = (int)substr($complaint->created_at, 0, 4);
                    $createdMonthStr = substr($complaint->created_at, 0, 7);
                    $createdMonth = (int)substr($complaint->created_at, 5, 2);
                    $createdQuarter = ceil($createdMonth / 3);

                    if ($year_from && $year_to) {
                        return $createdYear >= $year_from && $createdYear <= $year_to;
                    }

                    return (
                        (!$month || $createdMonthStr === $month) &&
                        (!$quarter || $createdQuarter == $quarter) &&
                        (!$year || $createdYear == $year)
                    );
                });

                $complaints_by_status = [
                    'pending' => $complaints->where('status', 'pending')->count(),
                    'in_progress' => $complaints->where('status', 'in_progress')->count(),
                    'resolved' => $complaints->where('status', 'resolved')->count(),
                    'reject' => $complaints->where('status', 'reject')->count(),
                ];

                $bookings = collect();
                try {
                    $bookings = $rooms->flatMap->bookings ?? collect();
                    $bookings = $bookings->filter(function ($booking) use ($month, $quarter, $year, $year_from, $year_to) {
                        $createdYear = (int)substr($booking->created_at, 0, 4);
                        $createdMonthStr = substr($booking->created_at, 0, 7);
                        $createdMonth = (int)substr($booking->created_at, 5, 2);
                        $createdQuarter = ceil($createdMonth / 3);

                        if ($year_from && $year_to) {
                            return $createdYear >= $year_from && $createdYear <= $year_to;
                        }

                        return (
                            (!$month || $createdMonthStr === $month) &&
                            (!$quarter || $createdQuarter == $quarter) &&
                            (!$year || $createdYear == $year)
                        );
                    });
                } catch (\Exception $e) {
                    Log::warning('Bookings not available');
                }

                $bookings_by_status = [
                    'pending' => $bookings->where('status', 'pending')->count(),
                    'approved' => $bookings->where('status', 'approved')->count(),
                    'rejected' => $bookings->where('status', 'rejected')->count(),
                    'waiting' => $bookings->where('status', 'waiting')->count(),
                ];

                return [
                    'name' => $property->name,
                    'revenue' => $revenue ?? 0,
                    'total_rooms' => $total_rooms,
                    'rented_rooms' => $rented_rooms,
                    'empty_rooms' => $empty_rooms,
                    'electric_cost' => $electric_cost ?? 0,
                    'water_cost' => $water_cost ?? 0,
                    'other_cost' => $other_cost ?? 0,
                    'complaints' => $complaints->count(),
                    'complaints_by_status' => $complaints_by_status,
                    'bookings' => $bookings->count(),
                    'bookings_by_status' => $bookings_by_status,
                    'income' => $revenue ?? 0,
                    'expense' => ($electric_cost + $water_cost + $other_cost) ?? 0,
                ];
            })->values();

            $startYear = $year_from ?? $year;
            $endYear = $year_to ?? $year;
            $monthlyIncomeExpense = collect();

            for ($currentYear = $startYear; $currentYear <= $endYear; $currentYear++) {
                for ($currentMonth = 1; $currentMonth <= 12; $currentMonth++) {
                    $bills = RoomBill::whereMonth('month', $currentMonth)
                        ->whereYear('month', $currentYear)
                        ->get();
                    
                    if (!empty($selected_properties)) {
                        $bills = $bills->filter(function($bill) use ($selected_properties) {
                            return in_array($bill->room->property->name ?? '', $selected_properties);
                        });
                    }

                    $income = $bills->sum('total');
                    $expense = $bills->sum('electric_total') + $bills->sum('water_total') + $bills->sum('other_total');
                    
                    $monthlyIncomeExpense->push([
                        'label' => "T{$currentMonth}/{$currentYear}",
                        'income' => $income,
                        'expense' => $expense
                    ]);
                }
            }

            $incomeExpenseStats = [
                'labels' => $monthlyIncomeExpense->pluck('label')->toArray(),
                'income' => $monthlyIncomeExpense->pluck('income')->toArray(),
                'expense' => $monthlyIncomeExpense->pluck('expense')->toArray(),
            ];

            $monthlyOccupancy = collect();
            for ($currentYear = $startYear; $currentYear <= $endYear; $currentYear++) {
                $yearlyOccupancy = $this->calculateOccupancyByContractStatus($currentYear, $selected_properties);
                foreach ($yearlyOccupancy as $monthIndex => $occupancy) {
                    $monthlyOccupancy->push([
                        'label' => "T" . ($monthIndex + 1) . "/{$currentYear}",
                        'occupancy' => $occupancy
                    ]);
                }
            }

            $occupancyChartData = [
                'labels' => $monthlyOccupancy->pluck('label')->toArray(),
                'occupancy' => $monthlyOccupancy->pluck('occupancy')->toArray()
            ];

            $summary = [
                'total_rooms' => $propertyStats->sum('total_rooms'),
                'total_rented' => $propertyStats->sum('rented_rooms'),
                'total_empty' => $propertyStats->sum('empty_rooms'),
                'total_revenue' => $propertyStats->sum('revenue'),
                'total_complaints' => $propertyStats->sum('complaints'),
                'total_bookings' => $propertyStats->sum('bookings'),
            ];

            $complaintsStats = [
                'pending' => $propertyStats->sum(function($stat) { return $stat['complaints_by_status']['pending'] ?? 0; }),
                'in_progress' => $propertyStats->sum(function($stat) { return $stat['complaints_by_status']['in_progress'] ?? 0; }),
                'resolved' => $propertyStats->sum(function($stat) { return $stat['complaints_by_status']['resolved'] ?? 0; }),
                'reject' => $propertyStats->sum(function($stat) { return $stat['complaints_by_status']['reject'] ?? 0; }),
            ];

            $bookingsStats = [
                'pending' => $propertyStats->sum(function($stat) { return $stat['bookings_by_status']['pending'] ?? 0; }),
                'approved' => $propertyStats->sum(function($stat) { return $stat['bookings_by_status']['approved'] ?? 0; }),
                'rejected' => $propertyStats->sum(function($stat) { return $stat['bookings_by_status']['rejected'] ?? 0; }),
                'waiting' => $propertyStats->sum(function($stat) { return $stat['bookings_by_status']['waiting'] ?? 0; }),
            ];

            $propertiesWithBookings = $propertyStats->filter(function($stat) {
                return $stat['bookings'] > 0;
            })->pluck('name')->toArray();

            return [
                'propertyStats' => $propertyStats,
                'summary' => $summary,
                'incomeExpenseStats' => $incomeExpenseStats,
                'occupancyChartData' => $occupancyChartData,
                'complaintsStats' => $complaintsStats,
                'bookingsStats' => $bookingsStats,
                'propertiesWithBookings' => $propertiesWithBookings,
                'revenueChartData' => [
                    'labels' => $monthlyIncomeExpense->pluck('label')->toArray(),
                    'revenue' => $monthlyIncomeExpense->pluck('income')->toArray()
                ]
            ];
        });

        return response()->json($data);
    }
}