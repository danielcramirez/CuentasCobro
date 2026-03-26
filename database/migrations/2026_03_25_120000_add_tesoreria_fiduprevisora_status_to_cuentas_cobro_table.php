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
        Schema::table('cuentas_cobro', function (Blueprint $table) {
            $table->enum('tesoreria_status', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente')->after('mayor_status');
            $table->text('tesoreria_comment')->nullable()->after('tesoreria_status');
            $table->foreignId('tesoreria_id')->nullable()->after('tesoreria_comment')->constrained('users')->nullOnDelete();
            $table->timestamp('tesoreria_reviewed_at')->nullable()->after('tesoreria_id');

            $table->enum('fiduprevisora_status', ['pendiente', 'en_tramite', 'pagado'])->default('pendiente')->after('tesoreria_reviewed_at');
            $table->text('fiduprevisora_comment')->nullable()->after('fiduprevisora_status');
            $table->foreignId('fiduprevisora_id')->nullable()->after('fiduprevisora_comment')->constrained('users')->nullOnDelete();
            $table->timestamp('fiduprevisora_reviewed_at')->nullable()->after('fiduprevisora_id');

            $table->index(['tesoreria_status', 'fiduprevisora_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuentas_cobro', function (Blueprint $table) {
            $table->dropIndex('cuentas_cobro_tesoreria_status_fiduprevisora_status_index');
            $table->dropConstrainedForeignId('fiduprevisora_id');
            $table->dropConstrainedForeignId('tesoreria_id');
            $table->dropColumn([
                'tesoreria_status',
                'tesoreria_comment',
                'tesoreria_reviewed_at',
                'fiduprevisora_status',
                'fiduprevisora_comment',
                'fiduprevisora_reviewed_at',
            ]);
        });
    }
};
