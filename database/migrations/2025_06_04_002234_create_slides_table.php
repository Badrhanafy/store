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
Schema::create('slides', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('subtitle');
    $table->string('cta_text');
    $table->string('image'); // This will store the filename
    $table->string('bg_color')->default('bg-gradient-to-r from-amber-100 to-pink-200');
    $table->integer('order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slides');
    }
};
