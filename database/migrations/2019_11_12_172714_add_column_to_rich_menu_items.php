<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToRichMenuItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rich_menu_items', function (Blueprint $table) {
            $table->string('rich_menu_id')->nullabe();
            $table->dropColumn('rich_menu_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rich_menu_items', function (Blueprint $table) {
            $table->dropColumn('rich_menu_id');
            $table->text('rich_menu_file');
        });
    }
}
