@extends('layouts.crow', ['title' => 'Administración | CROWFONDING'])
@section('content')
<section class="crow-section">
  <div class="section-heading"><p class="eyebrow">Administración</p><h1>Panel de gestión</h1><p>Gestiona tickets, equipos y cuentas.</p></div>
  @if($errors->any())<div class="notice error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
  <h2>Tickets</h2>
  @forelse($tickets as $ticket)
    <article class="ticket-card crow-ticket"><div class="crow-ticket-head"><h3>#{{ $ticket->id }} · {{ $ticket->user?->name ?? 'Cuenta eliminada' }}</h3><small>{{ $ticket->service }}</small></div><p>{{ $ticket->note }}</p><p>{{ $ticket->address }}</p><form class="inline-form" method="POST" action="{{ route('admin.tickets.update', $ticket) }}">@csrf @method('PATCH')<select name="status">@foreach($statuses as $status)<option @selected($ticket->status === $status)>{{ $status }}</option>@endforeach</select><button class="btn-main">Actualizar estado</button></form></article>
  @empty<p>No hay tickets.</p>@endforelse
  <h2>Categorías de componentes</h2>
  <form class="crow-admin-form" method="POST" action="{{ route('admin.component-categories.store') }}">
    @csrf
    <label for="component-category-name">Nombre de la categoría</label>
    <input id="component-category-name" name="name" value="{{ old('name') }}" maxlength="100" placeholder="Ej.: Memoria RAM, SSD, Gráficas" required>
    <label for="component-category-description">Descripción (opcional)</label>
    <textarea id="component-category-description" name="description" maxlength="500" placeholder="Breve descripción de la categoría"></textarea>
    <button class="btn-main" type="submit">Crear categoría</button>
  </form>
  <div class="admin-category-list">
    @forelse($componentCategories as $category)
      <article class="admin-product"><div><strong>{{ $category->name }}</strong><small>{{ $category->products_count }} productos</small></div><form method="POST" action="{{ route('admin.component-categories.delete', $category) }}" data-confirm="¿Eliminar la categoría {{ $category->name }}? Solo se eliminará si no contiene productos.">@csrf @method('DELETE')<button class="btn-danger" @disabled($category->products_count > 0)>Eliminar</button></form></article>
    @empty<p> todavía no creaste categorías.</p>@endforelse
  </div>
  <h2>Publicar producto</h2><form id="admin-product-form" class="crow-admin-form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">@csrf<label for="product-category">Tipo de producto</label><select id="product-category" name="category" required><option value="equipment" @selected(old('category', 'equipment') === 'equipment')>Equipo</option><option value="mug" @selected(old('category') === 'mug')>Mug</option><option value="component" @selected(old('category') === 'component')>Componente</option></select><div id="component-category-field" @unless(old('category') === 'component') hidden @endunless><label for="component-category-id">Categoría</label><select id="component-category-id" name="component_category_id" @required(old('category') === 'component')><option value="">Selecciona una categoría</option>@foreach($componentCategories as $category)<option value="{{ $category->id }}" @selected((string)old('component_category_id') === (string)$category->id)>{{ $category->name }}</option>@endforeach</select></div><label for="product-name">Nombre</label><input id="product-name" name="name" value="{{ old('name') }}" required><label for="product-price">Precio</label><input id="product-price" name="price" value="{{ old('price') }}" required><label for="product-description">Descripción</label><textarea id="product-description" name="description" required>{{ old('description') }}</textarea><label for="product-image">Imagen (opcional; JPG, PNG o WEBP, máx. 5 MB)</label><input id="product-image" type="file" name="image" accept="image/jpeg,image/png,image/webp"><button class="btn-main">Publicar producto</button></form>
  <h2>Productos publicados</h2>@forelse($products as $product)<article class="admin-product"><div><strong>@if($product->category === 'mug')Mug @elseif($product->category === 'component'){{ $product->componentCategory?->name ?? 'Componente' }} @else Equipo @endif: {{ $product->name }}</strong> · {{ $product->price }}</div><form method="POST" action="{{ route('admin.products.delete', $product) }}" data-confirm="¿Eliminar este producto?">@csrf @method('DELETE')<button class="btn-danger">Eliminar</button></form></article>@empty<p>No hay productos publicados.</p>@endforelse
  <h2>Crear cuenta</h2>
  <form id="admin-account-registration-form" data-account-registration class="crow-admin-form" method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    @include('crow.partials.registration-fields', ['withPassword' => true])
    <label for="role">Rol</label>
    <select id="role" name="role" required><option value="cliente" @selected(old('role', 'cliente') === 'cliente')>Cliente</option><option value="admin" @selected(old('role') === 'admin')>Administrador</option></select>
    <button class="btn-main">Crear cuenta</button>
  </form>
  @push('scripts')
    <script src="{{ asset('js/component-category-form.js') }}" defer></script>
    <script src="{{ asset('js/registration-person-type.js') }}" defer></script>
    <script src="{{ asset('js/register-validation.js') }}" defer></script>
  @endpush
  <h2>Usuarios</h2><div class="table-wrap"><table><thead><tr><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Teléfono</th><th>Rol</th><th>Guardar</th></tr></thead><tbody>@foreach($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->username }}</td><td>{{ $user->email }}</td><td>{{ $user->phone }}</td><td colspan="2"><form class="inline-form" method="POST" action="{{ route('admin.users.role', $user) }}">@csrf @method('PATCH')<select name="role"><option value="cliente" @selected($user->role === 'cliente')>Cliente</option><option value="admin" @selected($user->role === 'admin')>Administrador</option></select><button class="btn-main">Guardar rol</button></form></td></tr>@endforeach</tbody></table></div>
</section>
@endsection
