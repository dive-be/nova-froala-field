<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Illuminate\Http\Request;

final readonly class DiscardPendingAttachments
{
    public function __invoke(Request $request): void
    {
        PendingAttachment::forDraft($request->input('draftId'))
            ->get()
            ->each(static fn (PendingAttachment $attachment) => $attachment->prune());
    }
}
