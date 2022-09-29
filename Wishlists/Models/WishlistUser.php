<?php

namespace Extensions\Wishlists\Models;

use App\Models\User\User;

class WishlistUser extends User {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Get all of the user's wishlists.
     */
    public function wishlists() {
        return $this->hasMany(Wishlist::class, 'user_id');
    }
}
