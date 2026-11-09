@extends('layouts.crow', ['title' => $category->name.' | Componentes CROWFONDING'])

@section('content')
<section class="crow-section component-catalogue">
    <a class="catalogue-back-link" href="{{ route('components.index') }}">← Todas las categorías</a>
    <div class="section-heading">
        <p class="eyebrow">Componentes</p>
        <h1>{{ $category->name }}</h1>
        @if($category->description)<p>{{ $category->description }}</p>@endif
    </div>

    <div class="component-product-grid">
        @forelse($products as $product)
            <article class="component-product-card">
                <a class="component-product-image" href="{{ route('products.show', $product) }}" aria-label="Ver {{ $product->name }}">
                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy">
                </a>
                <div class="component-product-info">
                    <p class="component-product-category">{{ $category->name }}</p>
                    <h2>{{ $product->name }}</h2>
                    <p class="component-product-description">{{ $product->description }}</p>
                    <div class="component-product-footer">
                        <strong>{{ $product->price }}</strong>
                        <a href="{{ route('products.show', $product) }}">Ver producto <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state">Todavía no hay productos publicados en esta categoría.</div>
        @endforelse
    </div>

    @if($products->hasPages())<div class="catalogue-pagination">{{ $products->links() }}</div>@endif
</section>
@endsection
