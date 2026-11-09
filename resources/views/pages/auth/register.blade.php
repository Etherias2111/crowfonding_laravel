@extends('layouts.crow', ['title' => 'Crear cuenta | CROWFONDING'])

@section('content')
<section class="crow-form-wrap">
    <div class="crow-form">
        <p class="eyebrow">Únete a CROWFONDING</p>
        <h1>Crear cuenta</h1>
        <p>Selecciona si te registras como persona natural o jurídica. Necesitas una cuenta para solicitar y consultar tickets de servicio.</p>

        @if($errors->any())
        <div class="notice error" role="alert"><strong>Revisa los mensajes que aparecen debajo de cada campo. Conservamos los datos que ya escribiste.</strong></div>
        @endif

        <form id="account-registration-form" data-account-registration method="POST" action="{{ route('register.store') }}" novalidate>
            @csrf
            @include('crow.partials.registration-fields', ['withPassword' => true])
            <button class="btn-main" type="submit">Crear cuenta</button>
        </form>

        <p class="auth-switch">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>.</p>
    </div>
</section>
@push('scripts')
    <script src="{{ asset('js/registration-person-type.js') }}" defer></script>
    <script src="{{ asset('js/register-validation.js') }}" defer></script>
@endpush
@endsection
