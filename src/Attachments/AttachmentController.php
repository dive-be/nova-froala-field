<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Illuminate\Http\Response;
use Laravel\Nova\Http\Requests\NovaRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class AttachmentController extends Controller
{
    /** @throws NotFoundHttpException */
    public function destroy(NovaRequest $request): Response
    {
        ($this->getOrThrow($request)->detachCallback)($request);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
