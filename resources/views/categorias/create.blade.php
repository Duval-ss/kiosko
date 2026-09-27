@extends('layouts.app')

@section('title', 'Registrar categoría')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h1 class="h4 mb-0">
                        Registrar categoría
                    </h1>
                </div>

                <div class="card-body">
                    <form
                        action="{{ route('categorias.store') }}"
                        method="POST"
                    >
                        @csrf

                        @include('categorias._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection