@extends('layouts.app')
@section('title', 'Dashboard - CuentasCobro')
@section('content')
<main class="container-fluid bg-[#DFDFDF] min-h-screen p-0">
    <section>
    <h1>Crear Cuenta de Cobro</h1>

    <form action="{{ route('cuentas-cobro.store') }}" method="POST">
        @csrf
        <!-- Campos del formulario -->
        <div class="mb-3">
            <label for="fecha_emision" class="form-label">Fecha de Emisión:</label>
            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" required>
        </div>
        <div class="mb-3">
            <label for="proyecto_servicio" class="form-label">Proyecto/Servicio:</label>
            <input type="text" class="form-control" id="proyecto_servicio" name="proyecto_servicio" required>
        </div>
        <div class="mb-3">
            <label for="valor" class="form-label">Valor:</label>
            <input type="number" class="form-control" id="valor" name="valor" required>
        </div>
        <div class="mb-3"></div>
            <label for="documentos" class="form-label">Documentos:</label>
            <input type="file" class="form-control" id="documentos" name="documentos[]" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Crear Cuenta de Cobro</button>
    </form>
    </section>
</main>
@endsection