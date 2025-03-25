<?php

namespace App\Http\Controllers;

use App\Models\Ladderized;
use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;

class LadderizedController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'board_approval' => 'required|string',
            'program_cert' => 'required|string',
            'student_id' => 'required|exists:students,id',
            'graduation_date' => 'nullable|date',
        ]);

        // Create a new Ladderized record
        $ladderized = Ladderized::create([
            'board_approval' => $request->input('board_approval'),
            'program_cert' => $request->input('program_cert'),
            'student_id' => $request->input('student_id'),
            'graduation_date' => $request->input('graduation_date'),
        ]);

        // Return the newly created resource
        return new StudentResource($ladderized);
    }
}