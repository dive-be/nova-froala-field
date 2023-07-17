<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

final class Article extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;
}
