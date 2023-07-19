<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Illuminate\Http\Request;

final readonly class DetachAttachment
{
    public function __invoke(Request $request): void
    {
        Attachment::forUrl($request->input('src'))
            ->get()
            ->each(static fn (Attachment $a) => $a->prune());
    }
}
