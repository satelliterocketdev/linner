<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LineEvents\MessageEvent;
use Illuminate\Support\Facades\Log;

class SendSenario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendsenarios:scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'custome command to send a senario by using the scheduler task';

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
        $msg_Event = new MessageEvent();
        $msg_Event->sendScenarios();
    }
}
