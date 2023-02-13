<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsAllToMagazineTargets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magazine_targets', function (Blueprint $table) {
            $table->boolean('is_all')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magazine_targets', function (Blueprint $table) {
            Schema::table('magazine_targets', function (Blueprint $table) {
                $table->dropColumn('is_all');
            });
        });
    }
}
