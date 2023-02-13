<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnNameTagManegementIdToTagManagementIdFromRichMenuTargets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rich_menu_targets', function (Blueprint $table) {
            $table->renameColumn('tag_manegement_id', 'tag_management_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rich_menu_targets', function (Blueprint $table) {
            $table->renameColumn('tag_management_id', 'tag_manegement_id');
        });
    }
}
