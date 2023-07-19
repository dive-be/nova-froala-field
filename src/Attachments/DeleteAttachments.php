<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Froala\Nova\Froala;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

final readonly class DeleteAttachments
{
    public function __construct(public Froala $field) {}

    public function __invoke(Request $request, Model $model): array
    {
        Attachment::query()
            ->whereMorphedTo('attachable', $model)
            ->get()
            ->each(static fn (Attachment $a) => $a->prune());

        return [$this->field->attribute => ''];
    }
}
