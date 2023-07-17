<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Nova\Http\Requests\NovaRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class PendingAttachmentController extends Controller
{
    /** @throws NotFoundHttpException */
    public function store(NovaRequest $request): JsonResponse
    {
        $link = ($this->getOrThrow($request)->attachCallback)($request);

        return new JsonResponse(['link' => $link]);
    }

    /** @throws NotFoundHttpException */
    public function destroy(NovaRequest $request): Response
    {
        ($this->getOrThrow($request)->discardCallback)($request);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
