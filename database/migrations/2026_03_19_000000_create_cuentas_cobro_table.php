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
        Schema::create('cuentas_cobro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained('users')->cascadeOnDelete();
            $table->string('billing_month', 7); // Formato YYYY-MM
            $table->string('cuenta_pdf_path');
            $table->string('planilla_pdf_path');

            $table->enum('cuenta_status', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->enum('planilla_status', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->text('cuenta_supervisor_comment')->nullable();
            $table->text('planilla_supervisor_comment')->nullable();

            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('supervisor_reviewed_at')->nullable();

            $table->enum('mayor_status', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->text('mayor_comment')->nullable();
            $table->foreignId('mayor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('mayor_reviewed_at')->nullable();

            $table->timestamp('returned_at')->nullable();
            $table->timestamps();

            $table->index(['contractor_id', 'billing_month']);
            $table->index(['cuenta_status', 'planilla_status', 'mayor_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_cobro');
    }
};
