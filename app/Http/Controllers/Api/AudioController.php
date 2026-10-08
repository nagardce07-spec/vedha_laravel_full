<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookChapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AudioController extends Controller
{
    public function book(Book $book): BinaryFileResponse
    {
        abort_unless($book->upload_type === 'file' && $book->resource_path, 404);

        return $this->stream($book->resource_path);
    }

    public function chapter(BookChapter $chapter): BinaryFileResponse
    {
        abort_unless($chapter->upload_type === 'file' && $chapter->resource_path, 404);

        return $this->stream($chapter->resource_path);
    }

    private function stream(string $path): BinaryFileResponse
    {
        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        $absolutePath = $disk->path($path);
        $mimeType = $disk->mimeType($path) ?: 'audio/mpeg';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
