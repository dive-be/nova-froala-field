<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;

    protected $casts = ['email_verified_at' => 'datetime'];

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];
}
