<?php

namespace Extensions\TradeListings\Console;

use App\Models\Comment;
use Illuminate\Console\Command;

class MigrateTradeListingComments extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'migrate-trade-listing-comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates existing trade listing comments.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        //
        $this->info('Searching for existing trade listing comments...');

        $tradeListingComments = Comment::where('commentable_type', 'App\Models\TradeListing');

        $this->line('Migrating comments...');

        $tradeListingComments->update(['commentable_type' => 'Extensions\TradeListings\Models\TradeListing']);

        $this->line('Comments migrated.');

        return 0;
    }
}
