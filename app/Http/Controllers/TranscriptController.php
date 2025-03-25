<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Http\Request;

class TranscriptController extends Controller
{
    public function show($id)
    {
        $student = Students::with(['records', 'registrationInfos',
        'programsMajor',])
            ->findOrFail($id);
            
        return view('TOR', compact('student'));
    }
}