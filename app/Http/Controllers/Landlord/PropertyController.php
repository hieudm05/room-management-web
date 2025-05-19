<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Property ;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $listProperties = Property::all();
        dd($listProperties);
        return view("landlord.propertyManagement.list");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $Property )
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Property  $Property )
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property  $Property )
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property  $Property )
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property  $Property )
    {
        //
    }
}
