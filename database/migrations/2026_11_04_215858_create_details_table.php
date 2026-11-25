<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La tabla details ya fue creada por la migración anterior.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No elimina una tabla creada por otra migración.
    }
};
