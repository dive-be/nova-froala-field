<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Froala\Nova\Froala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

final readonly class StorePendingAttachment
{
    public function __construct(public Froala $field) {}

    public function __invoke(Request $request): string
    {
        $attachment = $request->file('attachment');

        if (config('froala.optimize_images')) {
            OptimizerChainFactory::create()->optimize($attachment->getPathname());
        }

        $pendingAttachment = PendingAttachment::create([
            'draft_id' => $request->input('draftId'),
            'attachment' => $attachment->store($this->field->getStorageDir(), $this->field->getStorageDisk()),
            'disk' => $this->field->getStorageDisk(),
        ]);

        return Storage::disk($this->field->getStorageDisk())->url($pendingAttachment->attachment);
    }
}
