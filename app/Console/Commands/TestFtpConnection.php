<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestFtpConnection extends Command
{
    protected $signature = 'ftp:test';
    protected $description = 'Prueba la conexión FTP configurada en Laravel';

    public function handle()
    {
        try {
            $files = Storage::disk('ftp')->files();
            $this->info("✅ Conexión exitosa. Archivos encontrados:");
            foreach ($files as $file) {
                $this->line($file);
            }
        } catch (\Exception $e) {
            $this->error("❌ Error al conectar al FTP: " . $e->getMessage());
        }
    }
}