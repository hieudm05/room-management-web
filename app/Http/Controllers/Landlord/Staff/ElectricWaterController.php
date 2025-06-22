<?php

namespace App\Http\Controllers\Landlord\Staff;


use App\Models\RoomUtility;
use Illuminate\Http\Request;
use App\Models\Landlord\Room;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class ElectricWaterController extends Controller
{
    //

    public function index(Room $room){
        $room->load('property', 'facilities', 'photos', 'services');
        return \view('landlord.Staff.rooms.electricWater.ElectriWater', compact('room'));
    }
     public function store(Request $request, Room $room)
    {
        $data = $request->validate([
            // (giữ nguyên như cũ)
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'electric_start' => 'nullable|integer',
            'electric_end' => 'nullable|integer',
            'kwh' => 'nullable|integer',
            'electricity' => 'required|string',
            'water_unit' => 'required|in:per_person,per_m3',
            'water_occupants' => 'nullable|integer',
            'water_m3' => 'nullable|numeric',
            'water' => 'required|string',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Format money (remove separators and "VND")
       $data['electricity'] = (int)str_replace([' VNĐ', '.', ','], '', $data['electricity']);
        $data['water'] = (int)str_replace([' VNĐ', '.', ','], '', $data['water']);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('utilities', 'public');
                $imagePaths[] = $path;
            }
        }

        $utility = RoomUtility::create([
            'room_id' => $room->room_id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'electric_start' => $data['electric_start'],
            'electric_end' => $data['electric_end'],
            'electric_kwh' => $data['kwh'],
            'electricity' => $data['electricity'],
            'water_unit' => $data['water_unit'],
            'water_occupants' => $data['water_occupants'],
            'water_m3' => $data['water_m3'],
            'water' => $data['water'],
        ]);

        if ($request->hasFile('images')) {
    foreach ($request->file('images') as $image) {
        $path = $image->store('utilities', 'public');

        $utility->photos()->create([
            'image_path' => $path
        ]);
    }
}

        return redirect()->back()->with('success', 'Utility data saved successfully.');
    }
}




