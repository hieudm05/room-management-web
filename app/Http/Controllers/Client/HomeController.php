<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room;
use Illuminate\Http\Request;

class HomeController extends Controller
{
public function renter()
{
    // Lấy danh sách phòng trọ mới nhất có phân trang
    $rooms = Room::latest()->paginate(6); // thêm paginate

    return view('home.render', compact('rooms'));
}

}
