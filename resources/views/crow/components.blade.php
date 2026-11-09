@extends('layouts.crow', ['title' => 'Componentes | CROWFONDING'])

@section('content')
<section class="crow-section component-catalogue">
    <div class="section-heading">
        <p class="eyebrow">Catálogo</p>
        <h1>Componentes</h1>
        <p>Elige una categoría para consultar los productos disponibles.</p>
    </div>

    <div class="component-category-grid component-category-grid-large">
        @forelse($categories as $category)
            <a class="component-category-card" href="{{ route('components.show', $category) }}">
                <span class="component-category-icon" aria-hidden="true">▦</span>
                <span class="component-category-name">{{ $category->name }}</span>
                @if($category->description)<span class="component-category-description">{{ $category->description }}</span>@endif
                <span class="component-category-count">{{ $category->products_count }} {{ $category->products_count === 1 ? 'producto' : 'productos' }}</span>
            </a>
        @empty
            <div class="empty-state">Aún no hay categorías. Vuelve pronto para consultar los componentes.</div>
        @endforelse
    </div>
</section>
@endsection
