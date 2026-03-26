<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE cuentas_cobro MODIFY cuenta_supervisor_comment MEDIUMTEXT NULL');
        DB::statement('ALTER TABLE cuentas_cobro MODIFY planilla_supervisor_comment MEDIUMTEXT NULL');
        DB::statement('ALTER TABLE cuentas_cobro MODIFY mayor_comment MEDIUMTEXT NULL');
        DB::statement('ALTER TABLE cuentas_cobro MODIFY tesoreria_comment MEDIUMTEXT NULL');
        DB::statement('ALTER TABLE cuentas_cobro MODIFY fiduprevisora_comment MEDIUMTEXT NULL');

        DB::statement('ALTER TABLE cuenta_cobro_documentos MODIFY comentario_supervisor MEDIUMTEXT NULL');
        DB::statement('ALTER TABLE cuenta_cobro_documentos MODIFY fiduprevisora_comentario MEDIUMTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE cuentas_cobro MODIFY cuenta_supervisor_comment TEXT NULL');
        DB::statement('ALTER TABLE cuentas_cobro MODIFY planilla_supervisor_comment TEXT NULL');
        DB::statement('ALTER TABLE cuentas_cobro MODIFY mayor_comment TEXT NULL');
        DB::statement('ALTER TABLE cuentas_cobro MODIFY tesoreria_comment TEXT NULL');
        DB::statement('ALTER TABLE cuentas_cobro MODIFY fiduprevisora_comment TEXT NULL');

        DB::statement('ALTER TABLE cuenta_cobro_documentos MODIFY comentario_supervisor TEXT NULL');
        DB::statement('ALTER TABLE cuenta_cobro_documentos MODIFY fiduprevisora_comentario TEXT NULL');
    }
};
