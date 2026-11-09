@extends('layouts.crow', ['title' => 'CROWFONDING | Ingeniería y Artes Gráficas'])

@section('content')
    <section id="inicio">
        <h2>Ingeniería y Artes Gráficas</h2>
        <p>Mantenimiento profesional de equipos de cómputo, venta de laptops garantizadas y diseño publicitario para potenciar tu marca.</p>
        <a href="{{ route('services') }}" class="btn-main">Pedir un Servicio / Ticket</a>
    </section>

    <section id="equipos">
        <h2>Equipos en Venta / Ofertas Destacadas</h2>
        <p>Explora los equipos disponibles y selecciona uno para ver sus características completas.</p>

        <div class="carrusel-wrapper" data-carousel aria-label="Equipos disponibles">
            <button class="carrusel-btn prev" type="button" aria-label="Ver equipos anteriores">&#10094;</button>
            <div class="carrusel-container">
                <div class="carrusel-track" aria-live="polite">
                    @forelse($products as $product)
                        <article class="laptop-card">
                            <a class="laptop-image-link" href="{{ route('products.show', $product) }}" aria-label="Ver detalles de {{ $product->name }}">
                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="laptop-img" loading="lazy">
                            </a>
                            <div class="laptop-info">
                                <span class="product-status">Disponible</span>
                                <h3>{{ $product->name }}</h3>
                                <p class="product-description">{{ $product->description }}</p>
                                <div class="product-card-footer">
                                    <strong class="product-price">{{ $product->price }}</strong>
                                    <a class="product-view-link" href="{{ route('products.show', $product) }}">Ver equipo <span aria-hidden="true">&#8594;</span></a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="empty-products-message">No hay equipos en venta publicados actualmente.</p>
                    @endforelse
                </div>
            </div>
            <button class="carrusel-btn next" type="button" aria-label="Ver más equipos">&#10095;</button>
        </div>
    </section>

    <section id="servicios-vitrina">
        <h2>Nuestros Servicios de Soporte</h2>
        <p>Atención técnica especializada. ¿Necesitas soporte? Haz clic <a href="{{ route('services') }}">aquí para abrir un ticket</a>.</p>
        <div class="grid-servicios">
            <div class="card-module">
                <h3>💻 Formateo de Equipos</h3>
                <p>Optimización, respaldo de información y reinstalación limpia del sistema operativo.</p>
            </div>
            <div class="card-module">
                <h3>🔍 Diagnóstico Técnico</h3>
                <p>Revisión profunda de hardware y software para detectar fallas ocultas o rendimiento bajo.</p>
            </div>
            <div class="card-module">
                <h3>🧹 Limpieza Física y Preventiva</h3>
                <p>Mantenimiento interno, cambio de pasta térmica y limpieza de componentes.</p>
            </div>
        </div>
    </section>

    <section id="mugs">
        <h2>Mugs</h2>
        <p>Mugs y artículos personalizados disponibles para la venta.</p>

        <div class="carrusel-wrapper" data-carousel aria-label="Mugs disponibles">
            <button class="carrusel-btn prev" type="button" aria-label="Ver mugs anteriores">&#10094;</button>
            <div class="carrusel-container">
                <div class="carrusel-track" aria-live="polite">
                    @forelse($mugs as $product)
                        <article class="laptop-card">
                            <a class="laptop-image-link" href="{{ route('products.show', $product) }}" aria-label="Ver detalles de {{ $product->name }}">
                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="laptop-img" loading="lazy">
                            </a>
                            <div class="laptop-info">
                                <span class="product-status">Disponible</span>
                                <h3>{{ $product->name }}</h3>
                                <p class="product-description">{{ $product->description }}</p>
                                <div class="product-card-footer">
                                    <strong class="product-price">{{ $product->price }}</strong>
                                    <a class="product-view-link" href="{{ route('products.show', $product) }}">Ver mug <span aria-hidden="true">&#8594;</span></a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="empty-products-message">Próximamente publicaremos mugs disponibles.</p>
                    @endforelse
                </div>
            </div>
            <button class="carrusel-btn next" type="button" aria-label="Ver más mugs">&#10095;</button>
        </div>
    </section>

    <section id="componentes">
        <h2>Venta de Componentes</h2>
        <p>Explora las categorías de componentes y consulta los productos disponibles.</p>
        <div class="component-category-grid">
            @forelse($componentCategories as $category)
                <a class="component-category-card" href="{{ route('components.show', $category) }}">
                    <span class="component-category-icon" aria-hidden="true">▦</span>
                    <span class="component-category-name">{{ $category->name }}</span>
                    <span class="component-category-count">{{ $category->products_count }} {{ $category->products_count === 1 ? 'producto' : 'productos' }}</span>
                </a>
            @empty
                <p class="empty-products-message">Próximamente publicaremos categorías de componentes.</p>
            @endforelse
        </div>
        <a class="btn-outline" href="{{ route('components.index') }}">Ver todas las categorías</a>
    </section>

    <section id="publicidad">
        <h2>Publicidad y Diseño</h2>
        <div class="grid-servicios">
            <div class="card-module">
                <h3>Branding y Artes Gráficas</h3>
                <p>Diseño de identidad visual, logotipos, piezas publicitarias y material POP para tu negocio.</p>
            </div>
        </div>
    </section>
@endsection
