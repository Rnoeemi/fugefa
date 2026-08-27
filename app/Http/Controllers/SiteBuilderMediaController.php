<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SiteBuilderMediaController extends Controller
{
    private const RELATIVE_DIR = 'images/site';

    private const MAX_BYTES = 20 * 1024 * 1024;

    /**
     * @var list<string>
     */
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif'];

    /**
     * @var list<string>
     */
    private const VIDEO_EXTENSIONS = ['mp4', 'webm', 'ogg'];

    public function index(): JsonResponse
    {
        $dir = $this->mediaDirectory();

        if (! is_dir($dir)) {
            return response()->json(['items' => []]);
        }

        $items = [];

        foreach (File::files($dir) as $file) {
            $ext = strtolower($file->getExtension());
            $type = $this->typeForExtension($ext);

            if ($type === null) {
                continue;
            }

            $name = $file->getFilename();

            $items[] = [
                'name' => $name,
                'url' => '/'.self::RELATIVE_DIR.'/'.$name,
                'type' => $type,
                'mtime' => $file->getMTime(),
                'size' => $file->getSize(),
            ];
        }

        usort($items, fn (array $a, array $b): int => $b['mtime'] <=> $a['mtime']);

        return response()->json(['items' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:'.(self::MAX_BYTES / 1024),
            ],
        ], [
            'file.required' => 'Válasszon fájlt.',
            'file.max' => 'A fájl maximum 20 MB lehet.',
        ]);

        /** @var UploadedFile $upload */
        $upload = $request->file('file');
        $ext = strtolower($upload->getClientOriginalExtension() ?: $upload->extension() ?: '');

        // Windows / böngésző néha üres vagy félreismert MIME-t ad; a kiterjesztés a forrás.
        if ($ext === '' || $ext === 'bin') {
            $guess = strtolower((string) $upload->guessExtension());
            if ($guess !== '') {
                $ext = $guess === 'jpeg' ? 'jpg' : $guess;
            }
        }
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        $type = $this->typeForExtension($ext);

        if ($type === null) {
            throw ValidationException::withMessages([
                'file' => ['Csak kép (jpg, png, gif, webp, svg, avif) vagy videó (mp4, webm, ogg) engedélyezett.'],
            ]);
        }

        $dir = $this->mediaDirectory();
        File::ensureDirectoryExists($dir);

        if (! is_writable($dir)) {
            return response()->json([
                'message' => 'A médiamappa nem írható (public/images/site). Ellenőrizze a jogosultságokat.',
            ], 500);
        }

        $base = Str::slug(pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'media';
        $filename = $base.'.'.$ext;
        $target = $dir.DIRECTORY_SEPARATOR.$filename;
        $i = 1;

        while (is_file($target)) {
            $filename = $base.'-'.$i.'.'.$ext;
            $target = $dir.DIRECTORY_SEPARATOR.$filename;
            $i++;
        }

        $upload->move($dir, $filename);

        if (! is_file($target)) {
            return response()->json(['message' => 'A fájl mentése nem sikerült.'], 500);
        }

        $url = '/'.self::RELATIVE_DIR.'/'.$filename;

        return response()->json([
            'item' => [
                'name' => $filename,
                'url' => $url,
                'type' => $type,
                'mtime' => time(),
                'size' => (int) filesize($target),
            ],
        ], 201);
    }

    protected function mediaDirectory(): string
    {
        return public_path(self::RELATIVE_DIR);
    }

    protected function typeForExtension(string $ext): ?string
    {
        if (in_array($ext, self::IMAGE_EXTENSIONS, true)) {
            return 'image';
        }

        if (in_array($ext, self::VIDEO_EXTENSIONS, true)) {
            return 'video';
        }

        return null;
    }
}
