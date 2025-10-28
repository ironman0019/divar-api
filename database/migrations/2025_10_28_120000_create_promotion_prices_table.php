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
        Schema::create('promotion_prices', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['ladder', 'special'])->comment('نردبان یا ویژه');
            $table->integer('duration_days')->comment('مدت زمان به روز');
            $table->decimal('price', 10, 2)->comment('قیمت به تومان');
            $table->boolean('is_active')->default(true)->comment('فعال/غیرفعال');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'duration_days', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_prices');
    }
};
