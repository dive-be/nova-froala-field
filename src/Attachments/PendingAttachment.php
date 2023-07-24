<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $attachment
 * @property Carbon $created_at
 * @property string $disk
 * @property string $draft_id
 * @property int    $id
 * @property Carbon $updated_at
 * @property string $url
 */
final class PendingAttachment extends Model
{
    use Prunable;

    protected $casts = ['id' => 'int'];

    protected $guarded = ['id'];

    protected $table = 'froala_pending_attachments';

    public static function forDraft(string $draftId): Builder
    {
        return self::query()->where('draft_id', $draftId);
    }

    public static function persistAll(string $draftId, Model $model): void
    {
        self::forDraft($draftId)
            ->get()
            ->each(static fn (self $attachment) => $attachment->persist($model));
    }

    public function persist(Model $model): void
    {
        Attachment::make($this->only('attachment', 'disk', 'url'))
            ->attachable()
            ->associate($model)
            ->save();

        $this->delete();
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => Storage::disk($this->disk)->url($this->attachment));
    }

    public function prune(): bool
    {
        Storage::disk($this->disk)->delete($this->attachment);

        return $this->delete();
    }

    public function prunable(): Builder
    {
        return $this->newQuery()->where(self::CREATED_AT, '<=', $this->freshTimestamp()->subDay());
    }
}
