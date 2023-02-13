<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeRichMenusItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rich_menu_items', function (Blueprint $table) {
            $table->integer('account_id');
            $table->text('rich_menu_file')->change();
            $table->boolean('is_active')->default(0)->change();
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
            $table->dropColumn('account_id');
            $table->string('rich_menu_file')->change();
            $table->boolean('is_active');
        });
    }
}
