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
        $field = $request
            ->newResource()
            ->availableFields($request)
            ->findFieldByAttribute($request->field);

        if (! $field instanceof Froala) {
            throw new NotFoundHttpException();
        }

        return $field;
    }
}
