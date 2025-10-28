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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('advertisement_id');
            $table->decimal('amount', 10, 2)->comment('مبلغ به تومان');
            $table->enum('payment_type', ['ladder', 'special'])->comment('نوع پرداخت: نردبان یا ویژه');
            $table->integer('duration_days')->comment('مدت زمان به روز');
            $table->text('description')->nullable()->comment('توضیحات');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('authority')->comment('شناسه درگاه');
            $table->string('ref_id')->nullable()->comment('شناسه تراکنش');
            $table->string('card_pan')->nullable()->comment('شماره کارت');
            $table->string('trace_no')->nullable()->comment('شماره پیگیری');
            $table->json('gateway_response')->nullable()->comment('پاسخ درگاه');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('advertisement_id')->references('id')->on('advertisements')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
