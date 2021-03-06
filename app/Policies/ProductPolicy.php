<?php

namespace App\Policies;

use App\Entities\User;
use App\Entities\Product;

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
        return !!(auth()->check() && $user->verified && $user->can('sellExternally')) || $user->isA('root');
    }

    /**
     * Determine if the user can add the stock-keeping unit to product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return bool
     */
    public function addSku(User $user, Product $product)
    {
        return !!(auth()->check() && $user->getMeta('has_sku'));
    }

    /**
     * Determine if the user can view the product.
     *
     * @param  User  $user
     * @param  Product  $product
     * @return bool
     */
    public function view(User $user, Product $product)
    {
        return !(!$product->is_active && $user->id !== $product->owner_id);
    }
}