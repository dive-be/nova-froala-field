<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Froala\Nova\Froala;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

final readonly class StorePendingAttachment
{
    public function __construct(public Froala $field) {}

    public function __invoke(Request $request): string
    {
        $this->abortIfFileNameExists($request);

        $attachment = PendingAttachment::create([
            'draft_id' => $request->input('draftId'),
            'attachment' => config('froala.preserve_file_names')
                ? $request->attachment->storeAs($this->field->getStorageDir(), $request->attachment->getClientOriginalName(), $this->field->disk)
                : $request->attachment->store($this->field->getStorageDir(), $this->field->disk),
            'disk' => $this->field->disk,
        ])->attachment;

        $this->imageOptimize($attachment);

        return Storage::disk($this->field->disk)->url($attachment);
    }

    private function abortIfFileNameExists(Request $request): void
    {
        $path = rtrim($this->field->getStorageDir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $request->attachment->getClientOriginalName();

        abort_if(config('froala.preserve_file_names') && Storage::disk($this->field->disk)->exists($path), Response::HTTP_CONFLICT);
    }

    // TODO - Make work for cloud filesystems
    private function imageOptimize(string $attachment): void
    {
        if (config('froala.optimize_images')) {
            $optimizerChain = OptimizerChainFactory::create();

            if (count($optimizers = config('froala.image_optimizers'))) {
                $optimizers = array_map(
                    static function (array $optimizerOptions, string $optimizerClassName) {
                        return (new $optimizerClassName())->setOptions($optimizerOptions);
                    },
                    $optimizers,
                    array_keys($optimizers)
                );

                $optimizerChain->setOptimizers($optimizers);
            }

            $optimizerChain->optimize(Storage::disk($this->field->disk)->path($attachment));
        }
    }
}
