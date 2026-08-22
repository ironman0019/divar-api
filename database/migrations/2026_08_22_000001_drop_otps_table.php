<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('otps');
    }

    public function down(): void
    {
        // OTP storage moved to Redis; table is not recreated on rollback.
    }
};
