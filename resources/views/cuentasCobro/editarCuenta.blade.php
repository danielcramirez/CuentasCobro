@extends('layouts.app')
@section('title', 'Detalle de Cuenta de Cobro - CuentasCobro')
@section('content')
<main class="container-fluid bg-[#DFDFDF] min-h-screen p-0">
    <h1>Editar Cuenta de Cobro</h1>

    <section>
    <form action="{{ route('cuentas-cobro.update', $cuenta->id) }}" method="POST">
        @csrf
        @method('PUT')
        <!-- Campos del formulario -->
        <div class="mb-3">
            <label for="fecha_emision" class="form-label">Fecha de Emisión:</label>
            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" value="{{ $cuenta->fecha_emision }}" required>
        </div>
        <div class="mb-3">
            <label for="proyecto_servicio" class="form-label">Proyecto/Servicio:</label>
            <input type="text" class="form-control" id="proyecto_servicio" name="proyecto_servicio" value="{{ $cuenta->proyecto_servicio }}" required>
        </div>
        <div class="mb-3">
            <label for="valor" class="form-label">Valor:</label>
            <input type="number" class="form-control" id="valor" name="valor" value="{{ $cuenta->valor }}" required>
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado:</label>
            <select class="form-select" id="estado" name="estado" required>
                <option value="borrador" {{ $cuenta->estado === 'borrador' ? 'selected' : '' }}>Borrador</option>
                <option value="pendiente" {{ $cuenta->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="pagado" {{ $cuenta->estado === 'pagado' ? 'selected' : '' }}>Pagado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Cuenta de Cobro</button>
    </form>
    </section>
</main>
@endsection