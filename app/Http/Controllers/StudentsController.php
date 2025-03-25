<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\StudentFormRequest;

class StudentsController extends Controller
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
    public function store(StudentFormRequest $request)
    {
        $validatedData = $request->validated();

        // Sanitize text fields
        $validatedData['last_name'] = Str::title(strip_tags($validatedData['last_name']));
        $validatedData['first_name'] = Str::title(strip_tags($validatedData['first_name']));
        $validatedData['middle_name'] = Str::title(strip_tags($validatedData['middle_name'] ?? ''));
        $validatedData['suffix'] = strtoupper(strip_tags($validatedData['suffix'] ?? ''));
        $validatedData['address'] = strip_tags($validatedData['address']);
        $validatedData['birthplace'] = strip_tags($validatedData['birthplace']);
        
        // Ensure numerical fields are correctly formatted
        $validatedData['gwa'] = number_format((float)$validatedData['gwa'], 2, '.', '');
        $validatedData['nstp_number'] = preg_replace('/[^A-Za-z0-9]/', '', $validatedData['nstp_number']);

        // Store data in the database
        Students::create($validatedData);

        return redirect()->back()->with('success', 'Student record added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Students $students)
    {
        // Implement the logic to display a specific student
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Students $students)
    {
        // Implement the logic to show the form for editing a specific student
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Students $students)
    {
        // Implement the logic to update a specific student
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Students $students)
    {
        // Implement the logic to remove a specific student
    }
}
