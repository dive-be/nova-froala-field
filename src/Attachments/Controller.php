<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Froala\Nova\Froala;
use Laravel\Nova\Http\Requests\NovaRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract readonly class Controller
{
    /** @throws NotFoundHttpException */
    protected function getOrThrow(NovaRequest $request): Froala
    {
        return $request
            ->newResource()
            ->availableFields($request)
            ->findFieldByAttribute($request->field, static fn () => throw new NotFoundHttpException());

        // TODO - This should work with Flexible content
    }
}
