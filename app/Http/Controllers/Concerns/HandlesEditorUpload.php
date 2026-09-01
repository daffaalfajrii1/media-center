<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait HandlesEditorUpload
{
    public function uploadEditorImage(Request $request): JsonResponse
    {
        $file = $request->file('file') ?? $request->file('upload');

        if (! $file) {
            return response()->json([
                'message' => 'File gambar wajib diunggah.',
            ], 422);
        }

        Validator::make(
            ['file' => $file],
            ['file' => ['required', 'image', 'max:5120']],
            ['file.image' => 'File harus berupa gambar (JPG, PNG, GIF, atau WebP).'],
        )->validate();

        $folder = $request->input('folder', 'pages/editor');
        $path = $file->store($folder, 'public');
        $url = asset('storage/'.$path);

        return response()->json([
            'url' => $url,
            'location' => $url,
        ]);
    }
}
