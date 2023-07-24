<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Nova\Http\Requests\NovaRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class ImageManagerController extends Controller
{
    /** @throws NotFoundHttpException */
    public function index(NovaRequest $request): JsonResponse
    {
        $images = ($this->findFieldOrFail($request)->imagesCallback)($request);

        return new JsonResponse($images);
    }

    /** @throws NotFoundHttpException */
    public function destroy(NovaRequest $request): Response
    {
        ($this->findFieldOrFail($request)->detachCallback)($request);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
