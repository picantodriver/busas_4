<?php

namespace App\Http\Controllers;

use App\Models\AcadYears;
use Illuminate\Http\Request;

class AcadYearsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'year' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        return redirect()->route('acad-years.index')->with('success', 'Academic Year created successfully.');

        }
    /**
     * Display the specified resource.
     */
    public function show(AcadYears $acadYears)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcadYears $acadYears)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcadYears $acadYears)
    {
        $validatedData = $request->validate([
            'year' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $acadYears->update($validatedData);

        return redirect()->route('acad-years.index')->with('success', 'Academic Year updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcadYears $acadYears)
    {
        //
    }
}
