<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSystemFolderTagsFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags_folders', function (Blueprint $table) {
            $table->boolean('system_folder')->default(0)->comment('0=通常,1=未分類フォルダ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags_folders', function (Blueprint $table) {
            $table->dropColumn('system_folder');
        });
    }
}
