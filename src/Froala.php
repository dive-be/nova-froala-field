<?php declare(strict_types=1);

namespace Froala\Nova;

use Closure;
use Froala\Nova\Attachments\DeleteAttachments;
use Froala\Nova\Attachments\DetachAttachment;
use Froala\Nova\Attachments\DiscardPendingAttachments;
use Froala\Nova\Attachments\GetAttachedImagesList;
use Froala\Nova\Attachments\PendingAttachment;
use Froala\Nova\Attachments\StorePendingAttachment;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

final class Froala extends Trix
{
    public $component = 'nova-froala-field';

    public $meta = ['options' => []];

    /**
     * The callback that should be executed to return attached images list.
     *
     * @var callable
     */
    public $imagesCallback;

    /**
     * Ability to pass any existing Froala options to the editor instance.
     * Refer to the Froala documentation {@link https://www.froala.com/wysiwyg-editor/docs/options}
     * to view a list of all available options.
     *
     * @param array $options
     * @return self
     */
    public function options(array $options): self
    {
        return $this->withMeta(['options' => $options]);
    }

    /**
     * Specify that file uploads should be allowed.
     */
    public function withFiles($disk = null, $path = null): self
    {
        $this->withFiles = true;

        return $this
            ->disk($disk ?? config('nova.froala.disk', $disk))
            ->path($path ?? config('nova.froala.path', DIRECTORY_SEPARATOR))
            ->attach(new StorePendingAttachment($this))
            ->detach(new DetachAttachment())
            ->delete(new DeleteAttachments($this))
            ->discard(new DiscardPendingAttachments())
            ->images(new GetAttachedImagesList($this))
            ->prunable();
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param  string $requestAttribute
     * @param  object $model
     * @param  string $attribute
     * @return Closure|null
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute): ?Closure
    {
        if (isset($this->fillCallback) && is_callable($this->fillCallback)) {
            return ($this->fillCallback)($request, $model, $attribute, $requestAttribute);
        }

        $this->fillAttributeFromRequest($request, $requestAttribute, $model, $attribute);

        if ($request->has("{$this->attribute}DraftId") && $this->withFiles) {
            return fn () => PendingAttachment::persistAll($request->input("{$this->attribute}DraftId"), $model);
        }

        return null;
    }

    /**
     * Get additional meta information to merge with the element payload.
     *
     * @return array<string, mixed>
     */
    public function meta(): array
    {
        $maxSize = $this->getUploadMaxFilesize();

        return array_merge([
            'draftId' => Str::orderedUuid(),
            'options' => array_merge(
                config('nova.froala.options', []),
                Arr::get($this->meta, 'options', []),
                ['fileMaxSize' => $maxSize, 'imageMaxSize' => $maxSize, 'videoMaxSize' => $maxSize]
            ),
        ], Arr::except($this->meta, 'options'));
    }

    /**
     * Specify the callback that should be used to get attached images list.
     *
     * @param  callable  $imagesCallback
     * @return $this
     */
    public function images(callable $imagesCallback): self
    {
        $this->withFiles = true;

        $this->imagesCallback = $imagesCallback;

        return $this;
    }

    /**
     * Get the path that the field is stored at on disk.
     *
     * @return string|null
     */
    public function getStorageDir(): string
    {
        return $this->storagePath ?? DIRECTORY_SEPARATOR;
    }

    /**
     * Get the full path that the field is stored at on disk.
     *
     * @return string
     */
    public function getStoragePath(): string
    {
        return DIRECTORY_SEPARATOR;
    }

    protected function getUploadMaxFilesize(): int
    {
        $uploadMaxFilesize = config('nova.froala.upload_max_filesize') ?? ini_get('upload_max_filesize');

        if (is_numeric($uploadMaxFilesize)) {
            return $uploadMaxFilesize;
        }

        $metric = mb_strtoupper(mb_substr($uploadMaxFilesize, -1));
        $uploadMaxFilesize = (int) $uploadMaxFilesize;

        return match ($metric) {
            'K' => $uploadMaxFilesize * 1024,
            'M' => $uploadMaxFilesize * 1048576,
            'G' => $uploadMaxFilesize * 1073741824,
            default => $uploadMaxFilesize,
        };
    }
}
