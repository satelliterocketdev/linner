<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPackageIdToMediaFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_files', function (Blueprint $table) {
            $table->integer('package_id')->nullable();;
        });

        \App\MediaFile::all()->each(function ($m) {
            switch ($m->tab) {
                case 'brown-cony-and-sally':
                    $m->package_id = '11537';
                    break;
                case 'choco-and-friends':
                    $m->package_id = '11538';
                    break;
                case 'universtar-bt21':
                    $m->package_id = '11539';
                    break;
            }
            $m->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_files', function (Blueprint $table) {
            $table->dropColumn('package_id');
        });
    }
}
