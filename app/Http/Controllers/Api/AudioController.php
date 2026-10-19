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
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'mp3' => 'audio/mpeg',
            'm4a', 'mp4' => 'audio/mp4',
            'wav' => 'audio/wav',
            default => $disk->mimeType($path) ?: 'application/octet-stream',
        };

        // Do not let browser/CDN caches keep playing the previous chapter file.
        // BinaryFileResponse also supports normal browser byte-range requests.
        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => sprintf('inline; filename="%s"', basename($path)),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
