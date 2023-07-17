<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property Model  $attachable
 * @property int    $attachable_id
 * @property string $attachable_type
 * @property string $attachment
 * @property Carbon $created_at
 * @property string $disk
 * @property int    $id
 * @property Carbon $updated_at
 * @property string $url
 */
final class Attachment extends Model
{
    use Prunable;

    protected $casts = ['attachable_id' => 'int', 'id' => 'int'];

    protected $guarded = ['id'];

    protected $table = 'nova_froala_attachments';

    public static function forUrl(string $url): Builder
    {
        return self::query()->where('url', $url);
    }

    public static function make(array $attributes): self
    {
        return new self($attributes);
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function prune(): void
    {
        Storage::disk($this->disk)->delete($this->attachment);

        $this->delete();
    }
}
