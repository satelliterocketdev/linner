<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToRichMenuTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rich_menu_targets', function (Blueprint $table) {
            $table->integer('pf_user_id')->nullable()->change();
            $table->integer('tag_manegement_id')->nullable()->change();
            $table->integer('scenario_id')->nullable();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->integer('index');
            $table->boolean('is_exclude')->default(false);
            $table->string('option');
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
            $table->integer('pf_user_id')->nullable(false)->change();
            $table->integer('tag_manegement_id')->nullable(false)->change();
            $table->dropColumn('scenario_id');
            $table->dropColumn('index');
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
            $table->dropColumn('is_exclude');
            $table->dropColumn('option');
        });
    }
}
