<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuentas_cobro', function (Blueprint $table) {
            if (!Schema::hasColumn('cuentas_cobro', 'supervisor_comment')) {
                $table->mediumText('supervisor_comment')->nullable()->after('planilla_supervisor_comment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cuentas_cobro', function (Blueprint $table) {
            if (Schema::hasColumn('cuentas_cobro', 'supervisor_comment')) {
                $table->dropColumn('supervisor_comment');
            }
        });
    }
};
