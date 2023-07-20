<?php declare(strict_types=1);

namespace Froala\Nova\Attachments;

use Froala\Nova\Froala;
use Laravel\Nova\Http\Requests\NovaRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract readonly class Controller
{
    private const TRANSLATABLE_SEPARATOR = '.';

    /** @throws NotFoundHttpException */
    protected function getOrThrow(NovaRequest $request): Froala
    {
        $field = $request
            ->newResource()
            ->availableFields($request)
            ->findFieldByAttribute($this->normalizeAndGetFieldAttribute($request));

        if (! $field instanceof Froala) {
            throw new NotFoundHttpException();
        }

        return $field;
    }

    /** Adds support for outl1ne/nova-translatable */
    private function normalizeAndGetFieldAttribute(NovaRequest $request): string
    {
        $attribute = $request->field;

        if (! is_string($attribute)) {
            return '';
        }

        if (! str_contains($attribute, self::TRANSLATABLE_SEPARATOR)) {
            return $attribute;
        }

        return current(explode(self::TRANSLATABLE_SEPARATOR, $attribute, 2));
    }
}
