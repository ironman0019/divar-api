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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('ads_type')->nullable()->comment('بازی');
            $table->string('ads_status')->default('as_good_as_new')->comment('در حد نو');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('status')->default(1)->comment('1 => enable, 0 => disable, 2 => active, 3 => pending, 4 => expire');
            $table->datetime('published_at')->nullable();
            $table->datetime('expired_at')->nullable();
            $table->unsignedBigInteger('view')->default(0);
            $table->string('contact')->nullable();
            $table->tinyInteger('is_special')->default(0);
            $table->tinyInteger('is_ladder')->default(0);
            $table->text('image')->nullable();
            $table->text('slug')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->text('tags')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->tinyInteger('willing_to_trade')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
