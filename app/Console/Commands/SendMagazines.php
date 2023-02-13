<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LineEvents\MessageEvent;

class SendMagazines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendMagazines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nothing special, just send magazines';

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
        $msg_Event->sendMagazines();
    }
}
