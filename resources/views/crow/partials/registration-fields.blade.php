@php($personType = old('person_type', 'natural'))
<label for="person_type">Tipo de persona</label>
<select id="person_type" name="person_type" data-person-type required>
    <option value="natural" @selected($personType === 'natural')>Persona natural</option>
    <option value="juridica" @selected($personType === 'juridica')>Persona jurídica</option>
</select>
@error('person_type')<small class="field-error">{{ $message }}</small>@enderror

<fieldset class="registration-fields" data-person-fields="natural" @if($personType !== 'natural') hidden disabled @endif>
    <legend>Datos de la persona natural</legend>
    <label for="first_name">Primer nombre</label>
    <input id="first_name" name="first_name" value="{{ old('first_name') }}" maxlength="50" autocomplete="given-name" required>
    @error('first_name')<small class="field-error">{{ $message }}</small>@enderror

    <label for="middle_name">Segundo nombre (opcional)</label>
    <input id="middle_name" name="middle_name" value="{{ old('middle_name') }}" maxlength="50">
    @error('middle_name')<small class="field-error">{{ $message }}</small>@enderror

    <label for="first_surname">Primer apellido</label>
    <input id="first_surname" name="first_surname" value="{{ old('first_surname') }}" maxlength="50" autocomplete="family-name" required>
    @error('first_surname')<small class="field-error">{{ $message }}</small>@enderror

    <label for="second_surname">Segundo apellido (opcional)</label>
    <input id="second_surname" name="second_surname" value="{{ old('second_surname') }}" maxlength="50">
    @error('second_surname')<small class="field-error">{{ $message }}</small>@enderror

    <label for="document_type">Tipo de documento</label>
    <select id="document_type" name="document_type" required>
        <option value="" disabled @selected(old('document_type') === null)>Selecciona el tipo de documento</option>
        <option value="TI" @selected(old('document_type') === 'TI')>TI</option>
        <option value="CC" @selected(old('document_type') === 'CC')>CC</option>
        <option value="CE" @selected(old('document_type') === 'CE')>Cédula de extranjería</option>
    </select>
    @error('document_type')<small class="field-error">{{ $message }}</small>@enderror

    <label for="document_number">Número de documento (sin dígito de verificación)</label>
    <input id="document_number" name="document_number" value="{{ old('document_number') }}" maxlength="50" autocomplete="off" required>
    @error('document_number')<small class="field-error">{{ $message }}</small>@enderror
</fieldset>

<fieldset class="registration-fields" data-person-fields="juridica" @if($personType !== 'juridica') hidden disabled @endif>
    <legend>Datos de la persona jurídica</legend>
    <label for="tax_id">NIT (sin dígito de verificación)</label>
    <input id="tax_id" name="tax_id" value="{{ old('tax_id') }}" inputmode="numeric" pattern="[0-9]{5,15}" maxlength="15" required>
    @error('tax_id')<small class="field-error">{{ $message }}</small>@enderror

    <label for="legal_name">Razón social</label>
    <input id="legal_name" name="legal_name" value="{{ old('legal_name') }}" maxlength="180" autocomplete="organization" required>
    @error('legal_name')<small class="field-error">{{ $message }}</small>@enderror

    <label for="representative_name">Nombre del representante legal</label>
    <input id="representative_name" name="representative_name" value="{{ old('representative_name') }}" maxlength="180" required>
    @error('representative_name')<small class="field-error">{{ $message }}</small>@enderror

    <label for="commercial_name">Nombre comercial</label>
    <input id="commercial_name" name="commercial_name" value="{{ old('commercial_name') }}" maxlength="180" required>
    @error('commercial_name')<small class="field-error">{{ $message }}</small>@enderror

</fieldset>

<label for="phone">Teléfono</label>
<input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" pattern="\+?[0-9]{7,15}" maxlength="16" required>
@error('phone')<small class="field-error">{{ $message }}</small>@enderror

<label for="phone_secondary">Teléfono adicional (opcional)</label>
<input id="phone_secondary" type="tel" name="phone_secondary" value="{{ old('phone_secondary') }}" autocomplete="tel" inputmode="tel" pattern="\+?[0-9]{7,15}" maxlength="16">
@error('phone_secondary')<small class="field-error">{{ $message }}</small>@enderror

<label for="address">Dirección</label>
<input id="address" name="address" value="{{ old('address') }}" autocomplete="street-address" maxlength="500" required>
@error('address')<small class="field-error">{{ $message }}</small>@enderror

<label for="department">Departamento</label>
<input id="department" name="department" value="{{ old('department') }}" autocomplete="address-level1" maxlength="100" required>
@error('department')<small class="field-error">{{ $message }}</small>@enderror

<label for="city">Ciudad o municipio</label>
<input id="city" name="city" value="{{ old('city') }}" autocomplete="address-level2" maxlength="100" required>
@error('city')<small class="field-error">{{ $message }}</small>@enderror

<label for="email">Correo electrónico</label>
<input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="254" required aria-invalid="@error('email') true @else false @enderror">
@error('email')<small class="field-error">{{ $message }}</small>@enderror

<label class="check-row" for="terms_accepted">
    <input id="terms_accepted" type="checkbox" name="terms_accepted" value="1" required @checked(old('terms_accepted'))>
    He leído y acepto los <a href="{{ route('terms') }}" target="_blank" rel="noopener">términos y condiciones</a>.
</label>
<small>Te recomendamos revisar los términos antes de crear la cuenta; podrás consultarlos nuevamente desde el enlace permanente al pie de la página.</small>
@error('terms_accepted')<small class="field-error">Debes aceptar los términos y condiciones para crear la cuenta.</small>@enderror

@if($withPassword ?? false)
    <label for="password">Contraseña</label>
    <input id="password" type="password" name="password" autocomplete="new-password" minlength="12" aria-describedby="password-help password-error" required>
    <small id="password-help">Mínimo 12 caracteres y un carácter especial. No repitas un dígito tres veces; evita secuencias como 123 o pares consecutivos como 2211.</small>
    <small id="password-error" class="field-error" aria-live="polite">@error('password'){{ $message }}@enderror</small>

    <label for="password_confirmation">Confirmar contraseña</label>
    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="12" required>
    @error('password_confirmation')<small class="field-error">{{ $message }}</small>@enderror
@endif
