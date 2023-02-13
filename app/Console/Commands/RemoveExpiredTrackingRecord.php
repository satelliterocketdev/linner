<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;
use Carbon\Carbon;
use App\ConversionTrackingUuid;
use App\ConversionTrackingRecord;

class RemoveExpiredTrackingRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:removeExpiredTrackingRecord';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete tracking data that has expired.';

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
     *
     * @return mixed
     */
    public function handle()
    {
        DB::transaction(function () {
            $now = Carbon::now();
            $deleted = ConversionTrackingUuid::where('expire_at', '<', $now)->delete();
            Log::debug('ConversionTrackingUuid schedule deleted RESULT: ' . $deleted);

            $deleted = ConversionTrackingRecord::where('expire_at', '<', $now)->delete();
            Log::debug('ConversionTrackingRecord schedule deleted RESULT: ' . $deleted);
        });
    }
}
