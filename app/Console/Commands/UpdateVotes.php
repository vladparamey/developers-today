<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateVotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:update-votes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to reset post upvotes count.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::table('posts')->update(['amount_upvotes' => 0]);
            DB::table('votes')->truncate();

            Log::info('Success update amount_upvotes to 0');
        } catch (Throwable $throwable) {
            Log::debug('Error message update amount_upvotes: ' . $throwable->getMessage());
        }
    }
}
