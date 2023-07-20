<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Froala\Nova\Froala;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

final readonly class StorePendingAttachment
{
    public function __construct(public Froala $field) {}

    public function __invoke(Request $request): string
    {
        $this->abortIfFileNameExists($attachment = $request->file('attachment'));
        $attachment = $this->optimize($attachment);

        $pendingAttachment = PendingAttachment::create([
            'draft_id' => $request->input('draftId'),
            'attachment' => $this->store($attachment),
            'disk' => $this->field->getStorageDisk(),
        ]);

        return Storage::disk($this->field->getStorageDisk())->url($pendingAttachment->attachment);
    }

    private function abortIfFileNameExists(UploadedFile $image): void
    {
        $path = rtrim($this->field->getStorageDir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $image->getClientOriginalName();

        abort_if(config('froala.preserve_file_names') && Storage::disk($this->field->getStorageDisk())->exists($path), Response::HTTP_CONFLICT);
    }

    private function optimize(UploadedFile $image): UploadedFile
    {
        if (! config('froala.optimize_images')) {
            return $image;
        }

        OptimizerChainFactory::create()->optimize(
            $image->getPathname()
        );

        return $image;
    }

    private function store(UploadedFile $image): string
    {
        return config('froala.preserve_file_names')
            ? $image->storeAs($this->field->getStorageDir(), $image->getClientOriginalName(), $this->field->getStorageDisk())
            : $image->store($this->field->getStorageDir(), $this->field->getStorageDisk());
    }
}
