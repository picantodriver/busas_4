<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\TranscriptController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('admin/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
Route::post('admin/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');

Route::get('/transcript/{student}', [TranscriptController::class, 'show'])
    ->name('transcript.show');

Route::get('/view-attachment/{studentId}/{studentName?}', function ($studentId, $studentName = null) {
    $student = \App\Models\Students::findOrFail($studentId);
    $record = $student->records()
        ->whereNotNull('attachment')
        ->first();

    if ($record && $record->attachment) {
        $tempFile = tempnam(sys_get_temp_dir(), 'attachment');
        file_put_contents($tempFile, $record->attachment);

        $finfo = finfo_open();
        $mimeType = finfo_buffer($finfo, $record->attachment, FILEINFO_MIME_TYPE);

        // Generate a filename for the Content-Disposition header
        $displayFileName = 'attachment';
        if ($record->attachment_name ?? null) {
            $displayFileName = $record->attachment_name;
        } elseif ($student->name ?? null) {
            $displayFileName = $student->name . '_document';
        }

        return response()->file($tempFile, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $displayFileName . '"'
        ]);
    }

    abort(404, 'Attachment not found');
})->name('view-attachment');
