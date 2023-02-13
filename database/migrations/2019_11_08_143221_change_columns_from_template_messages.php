<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsFromTemplateMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_messages', function (Blueprint $table) {
            $table->dropColumn('content_type');
            $table->integer('is_active')->default(0)->change();
            $table->boolean('is_draft')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_messages', function (Blueprint $table) {
            $table->string('content_type');
            $table->integer('is_active')->default(null)->change();
            $table->boolean('is_draft')->default(null)->change();
        });
    }
}
