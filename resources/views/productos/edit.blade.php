@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h1 class="h4 mb-0">
                        Editar producto
                    </h1>
                </div>

                <div class="card-body">
                    <form
                        action="{{ route('productos.update', $producto) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')

                        @include('productos._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection