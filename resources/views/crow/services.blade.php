@extends('layouts.crow', ['title' => 'Servicios y tickets | CROWFONDING'])
@section('content')<section class="crow-form-wrap"><div class="crow-form"><p class="eyebrow">Soporte técnico y diseño</p><h1>Solicitud de servicio / ticket</h1>
<div class="notice">Para enviar un ticket debes registrarte e iniciar sesión. Después de cada solicitud, espera al menos 60 minutos para enviar otra desde esta cuenta.</div>
@guest<div class="form-actions"><a class="btn-main" href="{{ route('register') }}">Crear una cuenta</a><a class="btn-outline" href="{{ route('login', ['next' => '/servicios']) }}">Iniciar sesión</a></div>@else
@if($errors->any())<div class="notice error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
@php($cooldownActive = $nextTicketAt && $nextTicketAt->isFuture())
@if($cooldownActive)<div class="notice error ticket-cooldown-notice" role="status"><strong>No puedes enviar otro ticket todavía.</strong><br>Podrás crear una nueva solicitud después de que termine el tiempo de espera de 60 minutos, a partir del {{ $nextTicketAt->format('d/m/Y H:i') }}.</div>@endif
<form method="POST" action="{{ route('tickets.store') }}">@csrf
<label for="service">Servicio</label><select id="service" name="service" required><option value="">Selecciona una opción</option>@foreach(['Formateo de Equipos','Diagnóstico Técnico','Limpieza Física y Preventiva','Instalación de Componentes (RAM/SSD)','Diseño Publicitario / Branding'] as $service)<option @selected(old('service') === $service)>{{ $service }}</option>@endforeach</select>
<label class="check-row" for="pickup"><input id="pickup" type="checkbox" name="pickup" value="1" @checked(old('pickup'))> Requiero recogida a domicilio</label>
<div id="pickup-address-fields" @unless(old('pickup')) hidden @endunless>
  <label for="address">Dirección de recogida</label>
  <input id="address" name="address" value="{{ old('address') }}" maxlength="500" @required(old('pickup')) autocomplete="street-address" placeholder="Escribe o pega la dirección">
  <div class="form-actions address-actions">
    <button class="btn-outline" id="use-current-location" type="button">Usar mi ubicación actual</button>
    <a class="btn-outline" id="search-address-map" href="https://www.google.com/maps" target="_blank" rel="noopener noreferrer">Buscar en Google Maps</a>
  </div>
  <small id="location-message" role="status">La ubicación requiere permiso del navegador. Puedes editar la dirección manualmente.</small>
</div>
<label for="note">Descripción del problema o solicitud</label><textarea id="note" name="note" maxlength="5000" required>{{ old('note') }}</textarea><button class="btn-main" type="submit" @disabled($cooldownActive)>Enviar solicitud</button></form>
@endguest</div></section>@endsection
