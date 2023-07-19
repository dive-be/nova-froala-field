<?php declare(strict_types=1);

namespace Tests;

use Froala\Nova\Froala;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

final class ArticleResource extends Resource
{
    public static string $model = Article::class;

    public static function uriKey(): string
    {
        return 'articles';
    }

    public function fields(Request $request): array
    {
        return [
            Text::make('Title'),
            Froala::make('Content')->withFiles(),
        ];
    }
}
