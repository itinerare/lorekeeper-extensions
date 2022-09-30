<?php

namespace Extensions\TradeListings\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class TradeListingsDatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run() {
        Model::unguard();

        // $this->call("OthersTableSeeder");
    }
}
