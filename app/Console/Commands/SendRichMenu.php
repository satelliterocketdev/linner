<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LineEvents\RichMenuEvent;

class SendRichMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendRichMenus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nothing special, just send richmenus';

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
        $richMenuEvent = new RichMenuEvent();
        $richMenuEvent->sendRichMenu();
        // $richMenuEvent->test();
    }
}
