<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // This import is required!

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            // 1. Basic Validation
            $request->validate([
                'image' => 'required|image|max:10240', // 10MB Limit
            ]);

            // 2. Upload
            if ($request->hasFile('image')) {
                // Save to 'storage/app/public/uploads'
                $path = $request->file('image')->store('uploads', 'public');

                if (!$path) {
                    return response()->json(['message' => 'File could not be saved to disk'], 500);
                }

                // 3. Return URL
                return response()->json([
                    'url' => asset('storage/' . $path)
                ]);
            }

            return response()->json(['message' => 'No image file found'], 400);

        } catch (\Exception $e) {
            // This will send the ACTUAL error back to your browser alert
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}