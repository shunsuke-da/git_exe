<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('memos', function (Blueprint $table) {
        $table->unsignedBigInteger('tag_id')->nullable()->after('user_id');
        $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     *///downメソッドはロールバックの時の動き、
     public function down()
     {
         Schema::table('memos', function (Blueprint $table) {
             if (Schema::hasColumn('memos', 'tag_id')) {
                 $table->dropForeign(['tag_id']);
                 $table->dropColumn('tag_id');
             }
         });
     }
     
};
