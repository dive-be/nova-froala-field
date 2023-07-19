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
    public const NAME = 'froala-field';

    public static bool $runsMigrations = true;

    public $component = self::NAME;

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
            ->disk($disk ?? config('froala.disk', $disk))
            ->path($path ?? config('froala.path', DIRECTORY_SEPARATOR))
            ->attach(new StorePendingAttachment($this))
            ->detach(new DetachAttachment())
            ->delete(new DeleteAttachments($this))
            ->discard(new DiscardPendingAttachments())
            ->images(new GetAttachedImagesList($this))
            ->prunable();
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute): ?Closure
    {
        if (is_callable($this->fillCallback)) {
            return ($this->fillCallback)($request, $model, $attribute, $requestAttribute);
        }

        $this->fillAttributeFromRequest($request, $requestAttribute, $model, $attribute);

        if ($request->has($draftId = "{$this->attribute}DraftId") && $this->withFiles) {
            return fn () => PendingAttachment::persistAll($request->input($draftId), $model);
        }

        return null;
    }

    /**
     * Get additional meta information to merge with the element payload.
     */
    public function meta(): array
    {
        $maxSize = $this->getUploadMaxFilesize();

        return array_merge([
            'draftId' => Str::orderedUuid(),
            'options' => array_merge(
                config('froala.options', []),
                Arr::get($this->meta, 'options', []),
                ['fileMaxSize' => $maxSize, 'imageMaxSize' => $maxSize, 'videoMaxSize' => $maxSize]
            ),
        ], Arr::except($this->meta, 'options'));
    }

    /**
     * Specify the callback that should be used to get attached images list.
     */
    public function images(callable $imagesCallback): self
    {
        $this->withFiles = true;

        $this->imagesCallback = $imagesCallback;

        return $this;
    }

    /**
     * Get the path that the field is stored at on disk.
     */
    public function getStorageDir(): string
    {
        return $this->storagePath;
    }

    /**
     * Get the full path that the field is stored at on disk.
     */
    public function getStoragePath(): string
    {
        return DIRECTORY_SEPARATOR;
    }

    protected function getUploadMaxFilesize(): int
    {
        $uploadMaxFilesize = config('froala.upload_max_filesize') ?? ini_get('upload_max_filesize');

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

    /**
     * Configure Froala to not register its migrations.
     */
    public static function ignoreMigrations(): void
    {
        self::$runsMigrations = false;
    }
}
