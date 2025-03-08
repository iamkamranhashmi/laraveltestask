<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendDeletionNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FileUploadController extends Controller
{
    // Show all uploaded files
    public function index()
    {
        $files = FileUpload::all();
        return view('welcome', compact('files'));
    }

    // Upload a file
    public function upload(Request $request)
    {
        // Validate file type and size
        $request->validate([
            'file' => 'required|file|mimes:pdf,docx|max:10240'
        ]);

        // Store file in the 'uploads' directory
        $file = $request->file('file');
        $path = $file->store('uploads');

        // Save file info to database
        $fileRecord = FileUpload::create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    // Delete a file manually
    public function delete(FileUpload $file)
    {
        // Delete file from storage
        Storage::delete($file->path);

        // Delete file record from database
        $file->delete();

        // Send deletion notification via RabbitMQ
        dispatch(new SendDeletionNotification($file->filename));

        return back()->with('success', 'File deleted.');
    }
public function deleteExpiredFiles()
{
    $expiredFiles = FileUpload::where('created_at', '<=', Carbon::now()->subMinutes(5))->get();

    if ($expiredFiles->isEmpty()) {
        Log::info('No expired files found.', ['current_time' => Carbon::now()]);
        return response()->json(['message' => 'No expired files found'], 200);
    }

    foreach ($expiredFiles as $file) {
        Log::info('Deleting file:', ['filename' => $file->filename, 'created_at' => $file->created_at]);

        // Delete file from storage
        if (Storage::exists($file->path)) {
            Storage::delete($file->path);
        }

        // Dispatch RabbitMQ notification job
        SendDeletionNotification::dispatch($file->filename);

        // Delete from database
        $file->delete();
    }

    return response()->json(['message' => 'Expired files deleted successfully'], 200);
}



}
