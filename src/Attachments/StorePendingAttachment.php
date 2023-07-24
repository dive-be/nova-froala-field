<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Froala\Nova\Froala;
use Illuminate\Http\Request;
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

        return PendingAttachment::create([
            'attachment' => $attachment->store($this->field->getStorageDir(), $disk = $this->field->getStorageDisk()),
            'draft_id' => $request->input('draftId'),
            'disk' => $disk,
        ])->url;
    }
}
