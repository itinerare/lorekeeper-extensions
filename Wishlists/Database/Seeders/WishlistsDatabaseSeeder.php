<?php

namespace Extensions\Wishlists\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class WishlistsDatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run() {
        Model::unguard();

        // $this->call("OthersTableSeeder");
    }
}
