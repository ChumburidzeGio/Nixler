<?php

namespace Modules\Product\Policies;

use Modules\User\Entities\User;
use Modules\Product\Entities\Product;

class ProductPolicy
{
    /**
     * Determine if the given product can be created by the user.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return bool
     */
    public function create(User $user, Product $product)
    {
        return auth()->check() && auth()->user()->shippingPrices()->count();
    }

    /**
     * Determine if the given product can be updated by the user.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return bool
     */
    public function update(User $user, Product $product)
    {
        return !!(auth()->check() && $product->owner_id == $user->id);
    }

    /**
     * Determine if the user can add the link to external shop for current product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return bool
     */
    public function sellExternally(User $user, Product $product)
    {
        return !!(auth()->check() && $user->verified);
    }
}