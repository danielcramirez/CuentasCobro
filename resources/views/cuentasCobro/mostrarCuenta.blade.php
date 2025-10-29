@extends('layouts.app')
@section('title', 'Dashboard - CuentasCobro')
@section('content')
<main class="container-fluid bg-[#DFDFDF] min-h-screen p-0">
    <h1>Cuentas de cobro creadas</h1>

    <section>
        @if($cuentas->isEmpty())
            <p>No hay cuentas de cobro creadas.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha de Emisión</th>
                        <th>Proyecto/Servicio</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cuentas as $cuenta)
                        <tr>
                            <td>{{ $cuenta->user->name }} - {{ $cuenta->user->role->name }}</td>
                            <td>{{ $cuenta->fecha_emision }}</td>
                            <td>{{ $cuenta->proyecto_servicio }}</td>
                            <td>{{ $cuenta->valor }}</td>
                            <td>{{ $cuenta->estado }}</td>
                            <td>
                                <a href="{{ route('cuentas-cobro.edit', $cuenta->id) }}" class="btn btn-info btn-sm">Editar</a>

                                <form action="{{ route('cuentas-cobro.destroy', $cuenta->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta cuenta de cobro? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </section>
</main>
@endsection