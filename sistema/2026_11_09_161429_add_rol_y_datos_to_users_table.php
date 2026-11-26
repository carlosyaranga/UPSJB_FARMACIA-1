
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (! Schema::hasColumn('users', 'rol_id')) {
                $table->foreignId('rol_id')
                    ->nullable()
                    ->constrained('roles')
                    ->restrictOnDelete();
            }

            if (! Schema::hasColumn('users', 'dni')) {
                $table->char('dni', 8)->nullable()->unique();
            }

            if (! Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono', 15)->nullable();
            }

            if (! Schema::hasColumn('users', 'colegiatura')) {
                $table->string('colegiatura', 20)->nullable();
            }

        });
    }

    public function down(): void
    {
        // No eliminar columnas existentes para proteger los datos.
    }
};
