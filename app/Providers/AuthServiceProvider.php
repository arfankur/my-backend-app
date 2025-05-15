<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\Cart;
use App\Policies\ItemPolicy;
use App\Policies\CartPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Item::class => ItemPolicy::class,
        Cart::class => CartPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
} 