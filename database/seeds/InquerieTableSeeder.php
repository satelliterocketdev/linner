<?php

use Illuminate\Database\Seeder;
use App\Inquery;
use App\Account;
use App\User;

class InquerieTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inqueries = new Inquery();
        $inqueries->account_id = Account::first()->id;
        $inqueries->body = '本文です。本文です。本文です。本文です。本文です。本文です。本文です。本文です。本文です。本文です。本文です。本文です。本文です。';
        $inqueries->answer = '回答です。回答です。回答です。回答です。回答です。回答です。回答です。回答です。回答です。回答です。回答です。回答です。回答です。';
        $inqueries->save();
    }
}
