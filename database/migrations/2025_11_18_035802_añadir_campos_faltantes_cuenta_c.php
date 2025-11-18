<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // Agregar campos faltantes: descripcion y ruta_archivo
        if (Schema::hasTable('cuenta_cobros')) {
            Schema::table('cuenta_cobros', function (Blueprint $table) {
                if (!Schema::hasColumn('cuenta_cobros', 'descripcion')) {
                    $table->text('descripcion')->nullable()->after('proyecto_servicio');
                }
                if (!Schema::hasColumn('cuenta_cobros', 'ruta_archivo')) {
                    $table->string('ruta_archivo')->nullable()->after('descripcion');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cuenta_cobros')) {
            Schema::table('cuenta_cobros', function (Blueprint $table) {
                if (Schema::hasColumn('cuenta_cobros', 'ruta_archivo')) {
                    $table->dropColumn('ruta_archivo');
                }
                if (Schema::hasColumn('cuenta_cobros', 'descripcion')) {
                    $table->dropColumn('descripcion');
                }
            });
        }
    }
};
