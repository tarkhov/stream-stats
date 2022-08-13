<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 255);
            $table->string('user_login', 255);
            $table->string('user_name', 255);
            $table->string('game_id', 255);
            $table->string('game_name', 255);
            $table->string('type', 255);
            $table->string('title', 255);
            $table->integer('viewer_count')->unsigned();
            $table->timestamp('started_at')->useCurrent();
            $table->string('language', 255);
            $table->string('thumbnail_url', 255);
            $table->text('tag_ids');
            $table->boolean('is_mature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('streams');
    }
};
