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
        // Las columnas ya fueron agregadas por la migración anterior.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No elimina columnas creadas por otra migración.
    }
};
