
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La tabla roles ya fue creada
        // por una migración anterior.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No eliminar la tabla roles.
    }
};
