<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Support\CartManager;

class MergeCartAfterLogin
{
    public function __construct(protected CartManager $cart) {}

    public function handle(Login $event): void
    {
        $this->cart->mergeSessionToUser($event->user->id);
    }
}
