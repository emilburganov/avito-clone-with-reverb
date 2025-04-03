<?php

use App\Http\Middleware\TokenCheck;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => [TokenCheck::class]]);

Broadcast::channel('ad-featured.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});
