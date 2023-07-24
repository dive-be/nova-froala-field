<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Froala\Nova\Froala;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

final readonly class GetAttachedImagesList
{
    public function __construct(public Froala $field) {}

    public function __invoke(Request $request): Collection
    {
        $disk = Storage::disk($this->field->disk);

        return Collection::make($disk->allFiles(config('froala.path')))
            ->filter(static fn (string $file) => str_starts_with((string) $disk->mimeType($file), 'image'))
            ->map($disk->url(...))
            ->map(static fn (string $url) => ['thumb' => $url, 'url' => $url]);
    }
}
