<?php

namespace Tests\Feature;

use App\Models\CuentaCobro;
use App\Models\CuentaCobroDocumento;
use App\Models\Roles;
use App\Models\User;
use App\Notifications\CuentaCobroFlowNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CuentaCobroFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected User $contratista;
    protected User $apoyo;
    protected User $supervisor;
    protected User $admin;
    protected User $centralDeCuentas;
    protected User $fiduprevisora;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->contratista = $this->createUserWithRole('contratista', 'contratista.test@cuentascobro.local');
        $this->apoyo = $this->createUserWithRole('apoyo a la supervisión', 'apoyo.test@cuentascobro.local');
        $this->supervisor = $this->createUserWithRole('supervisor', 'supervisor.test@cuentascobro.local');
        $this->admin = $this->createUserWithRole('admin', 'admin.test@cuentascobro.local');
        $this->centralDeCuentas = $this->createUserWithRole('central de cuentas', 'tesoreria.test@cuentascobro.local');
        $this->fiduprevisora = $this->createUserWithRole('fiduprevisora', 'fiduprevisora.test@cuentascobro.local');
    }

    public function test_complete_approval_flow_notifies_all_participants(): void
    {
        Storage::fake('local');
        Notification::fake();

        $this->actingAs($this->contratista)
            ->post(route('cuentas.store'), [
                'billing_month' => '2026-04',
                'numero_cuenta' => 2,
                'documentos' => $this->fakeDocuments([1, 2, 3, 4, 5, 6, 17]),
            ])
            ->assertRedirect(route('cuentas.index'));

        $cuenta = CuentaCobro::query()->latest('id')->firstOrFail();
        $documentos = $cuenta->documentos()->orderBy('numero_documento')->get();

        $this->assertCount(7, $documentos);
        $this->assertSame('pendiente', $cuenta->cuenta_status);
        $this->assertSame('pendiente', $cuenta->planilla_status);
        $this->assertSame('pendiente', $cuenta->mayor_status);
        $this->assertSame('pendiente', $cuenta->tesoreria_status);
        $this->assertSame('pendiente', $cuenta->fiduprevisora_status);

        Notification::assertSentTo($this->apoyo, CuentaCobroFlowNotification::class);
        Notification::assertSentTo($this->contratista, CuentaCobroFlowNotification::class);

        $this->actingAs($this->apoyo);

        foreach ($documentos as $documento) {
            $this->post(route('cuentas.documento.review', [$cuenta, $documento]), [
                'decision' => 'aprobada',
                'comentario' => 'Documento aprobado.',
            ])->assertRedirect(route('cuentas.show', $cuenta));
        }

        $cuenta->refresh();

        $this->assertSame('aprobada', $cuenta->cuenta_status);
        $this->assertSame('aprobada', $cuenta->planilla_status);
        $this->assertSame($this->apoyo->id, $cuenta->supervisor_id);

        Notification::assertSentTo($this->supervisor, CuentaCobroFlowNotification::class);

        $this->actingAs($this->supervisor)
            ->post(route('cuentas.documentos.firmados.upload', $cuenta), [
                'documento_1_firmado' => UploadedFile::fake()->create('documento-1-firmado.pdf', 100, 'application/pdf'),
                'documento_2_firmado' => UploadedFile::fake()->create('documento-2-firmado.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('cuentas.show', $cuenta));

        $cuenta->refresh();

        $this->assertNotNull($cuenta->documento_1_firmado_path);
        $this->assertNotNull($cuenta->documento_2_firmado_path);

        Notification::assertSentTo($this->centralDeCuentas, CuentaCobroFlowNotification::class);

        $this->actingAs($this->centralDeCuentas)
            ->post(route('cuentas.tesoreria.review', $cuenta), [
                'decision' => 'aprobada',
                'comment' => 'Remitida a Fiduprevisora.',
            ])
            ->assertRedirect(route('cuentas.show', $cuenta));

        $cuenta->refresh();

        $this->assertSame('aprobada', $cuenta->tesoreria_status);
        $this->assertSame('en_revision', $cuenta->fiduprevisora_status);

        Notification::assertSentTo($this->fiduprevisora, CuentaCobroFlowNotification::class);

        $this->actingAs($this->fiduprevisora);

        foreach ($cuenta->documentos()->orderBy('numero_documento')->get() as $documento) {
            $this->post(route('cuentas.fiduprevisora.documento.review', [$cuenta, $documento]), [
                'decision' => 'aprobada',
                'comentario' => 'Documento aprobado por Fiduprevisora.',
            ])->assertRedirect(route('cuentas.show', $cuenta));
        }

        $cuenta->refresh();

        $this->assertSame('en_tramite', $cuenta->fiduprevisora_status);

        $this->actingAs($this->fiduprevisora)
            ->post(route('cuentas.fiduprevisora.review', $cuenta), [
                'status' => 'pagado',
                'comment' => 'Pago confirmado.',
            ])
            ->assertRedirect(route('cuentas.show', $cuenta));

        $cuenta->refresh();

        $this->assertSame('pagado', $cuenta->fiduprevisora_status);

        Notification::assertSentTo($this->contratista, CuentaCobroFlowNotification::class);
        Notification::assertSentTo($this->apoyo, CuentaCobroFlowNotification::class);
        Notification::assertSentTo($this->supervisor, CuentaCobroFlowNotification::class);
        Notification::assertSentTo($this->centralDeCuentas, CuentaCobroFlowNotification::class);
        Notification::assertSentTo($this->fiduprevisora, CuentaCobroFlowNotification::class);
    }

    public function test_all_operational_roles_can_open_dashboard(): void
    {
        foreach ([
            $this->contratista,
            $this->apoyo,
            $this->supervisor,
            $this->admin,
            $this->centralDeCuentas,
            $this->fiduprevisora,
        ] as $user) {
            $this->actingAs($user)
                ->get(route('dashboard'))
                ->assertOk();
        }
    }

    public function test_apoyo_can_open_cuenta_detail_and_see_documents(): void
    {
        Storage::fake('local');

        $this->actingAs($this->contratista)
            ->post(route('cuentas.store'), [
                'billing_month' => '2026-05',
                'numero_cuenta' => 2,
                'documentos' => $this->fakeDocuments([1, 2, 3, 4, 5, 6, 17]),
            ])
            ->assertRedirect(route('cuentas.index'));

        $cuenta = CuentaCobro::query()->latest('id')->firstOrFail();

        $this->actingAs($this->apoyo)
            ->get(route('cuentas.show', $cuenta))
            ->assertOk()
            ->assertSee('Visor documental')
            ->assertSee('Doc 1')
            ->assertSee('Revision por documento en apoyo a supervision');
    }

    public function test_contratista_only_resubmits_rejected_document_after_supervision_return(): void
    {
        Storage::fake('local');

        $this->actingAs($this->contratista)
            ->post(route('cuentas.store'), [
                'billing_month' => '2026-06',
                'numero_cuenta' => 2,
                'documentos' => $this->fakeDocuments([1, 2, 3, 4, 5, 6, 17]),
            ])
            ->assertRedirect(route('cuentas.index'));

        $cuenta = CuentaCobro::query()->latest('id')->firstOrFail();
        $documentoCuenta = $cuenta->documentos()->where('numero_documento', 1)->firstOrFail();
        $documentoDos = $cuenta->documentos()->where('numero_documento', 2)->firstOrFail();
        $rutaOriginalDoc2 = $documentoDos->archivo_path;

        $this->actingAs($this->apoyo)
            ->post(route('cuentas.documento.review', [$cuenta, $documentoCuenta]), [
                'decision' => 'rechazada',
                'comentario' => 'Corrige solo este documento.',
            ])
            ->assertRedirect(route('cuentas.show', $cuenta));

        $this->actingAs($this->contratista)
            ->get(route('cuentas.show', $cuenta))
            ->assertOk()
            ->assertSee('Solo debes reemplazar los documentos rechazados')
            ->assertSee('Corrige solo este documento.')
            ->assertDontSee('2. Informe de gestión obligaciones contractuales SECOP II');

        $this->actingAs($this->contratista)
            ->post(route('cuentas.resubmit', $cuenta), [
                'billing_month' => '2026-06',
                'documentos' => [
                    1 => UploadedFile::fake()->create('documento-1-corregido.pdf', 100, 'application/pdf'),
                ],
            ])
            ->assertRedirect(route('cuentas.show', $cuenta));

        $cuenta->refresh();
        $documentoCuenta->refresh();
        $documentoDos->refresh();

        $this->assertNull($cuenta->returned_at);
        $this->assertNull($cuenta->returned_stage);
        $this->assertSame('pendiente', $cuenta->cuenta_status);
        $this->assertSame('pendiente', $cuenta->planilla_status);
        $this->assertSame('cargado', $documentoCuenta->estado);
        $this->assertNull($documentoCuenta->comentario_supervisor);
        $this->assertSame($rutaOriginalDoc2, $documentoDos->archivo_path);
    }

    private function createUserWithRole(string $roleName, string $email): User
    {
        $role = Roles::query()->where('name', $roleName)->firstOrFail();

        return User::factory()->create([
            'name' => ucfirst($roleName),
            'email' => $email,
            'role_id' => $role->id,
        ]);
    }

    private function fakeDocuments(array $numbers): array
    {
        $documents = [];

        foreach ($numbers as $number) {
            $documents[$number] = UploadedFile::fake()->create("documento-{$number}.pdf", 100, 'application/pdf');
        }

        return $documents;
    }
}
