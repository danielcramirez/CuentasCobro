<?php

namespace Database\Seeders;

use App\Models\CuentaCobro;
use App\Models\CuentaCobroDocumento;
use Illuminate\Database\Seeder;

class CuentaCobroDocumentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogo = CuentaCobroDocumento::getCatalogo();
        
        // Para cada cuenta de cobro existente, inicializar documentos requeridos
        CuentaCobro::all()->each(function ($cuenta) use ($catalogo) {
            $numero_cuenta = $cuenta->getNumeroCuenta();
            $documentos_requeridos = CuentaCobroDocumento::getDocumentosRequeridos($numero_cuenta);
            
            foreach ($documentos_requeridos as $numero) {
                CuentaCobroDocumento::updateOrCreate(
                    [
                        'cuenta_cobro_id' => $cuenta->id,
                        'numero_documento' => $numero,
                    ],
                    [
                        'nombre_documento' => $catalogo[$numero],
                        'estado' => 'pendiente',
                    ]
                );
            }
        });
        
        $this->command->info('Documentos de cuentas de cobro inicializados correctamente.');
    }
}
