<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->integer('user_id');
            $table->timestamps();
    });
        // ここで、user_idは、usersテーブルのidを参照する外部キー制約を追加することができます。
        // ただし、usersテーブルがすでに存在している必要があります。
        // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
