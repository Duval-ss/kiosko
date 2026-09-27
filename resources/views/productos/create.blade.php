@extends('layouts.app')

@section('title', 'Registrar producto')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h1 class="h4 mb-0">
                        Registrar producto
                    </h1>
                </div>

                <div class="card-body">
                    @if ($categorias->isEmpty())
                        <div class="alert alert-warning">
                            Primero debes registrar una categoría activa.
                        </div>

                        <a
                            href="{{ route('categorias.create') }}"
                            class="btn btn-success"
                        >
                            Registrar categoría
                        </a>
                    @else
                        <form
                            action="{{ route('productos.store') }}"
                            method="POST"
                        >
                            @csrf

                            @include('productos._form')
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection