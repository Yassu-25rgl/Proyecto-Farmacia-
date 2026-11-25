@extends('template.administrador')

@section('title', 'CATEGORÍAS')
@section('name_section', 'CATEGORÍAS')
@section('description', 'Actualizar categoría')
@section('item_section', 'Actualizar registro')
@section('section', 'Categorías')
@section('module', 'Configuraciones')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        {{-- PUT /categories/{id} → CategoryController@update --}}
        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT') 
            
            {{-- Incluye el formulario compartido que tiene los inputs --}}
            @include('settings.categories.form')
        </form>
    </div>
</div>
@endsection