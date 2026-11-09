document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[data-account-registration]').forEach(form => {
    const password = form.querySelector('[name="password"]');
    const confirmation = form.querySelector('[name="password_confirmation"]');
    const error = form.querySelector('#password-error');
    if (!password || !error) return;

    const messages = value => {
      const problems = [];
      if ([...value].length < 12) problems.push('Usa al menos 12 caracteres.');
      if (!/[^A-Za-z0-9]/.test(value)) problems.push('Incluye un carácter especial.');
      if (/([0-9])\1\1/.test(value)) problems.push('No repitas un dígito tres veces seguidas.');

      for (let i = 0; i <= value.length - 3; i++) {
        const digits = value.slice(i, i + 3);
        const firstStep = Number(digits[1]) - Number(digits[0]);
        const secondStep = Number(digits[2]) - Number(digits[1]);
        if (/^\d{3}$/.test(digits) && ((firstStep === 1 && secondStep === 1) || (firstStep === -1 && secondStep === -1))) {
          problems.push('Evita secuencias numéricas como 123 o 321.');
          break;
        }
      }

      for (let i = 0; i <= value.length - 4; i++) {
        const digits = value.slice(i, i + 4);
        if (/^(\d)\1(\d)\2$/.test(digits) && Math.abs(Number(digits[0]) - Number(digits[2])) === 1) {
          problems.push('Evita pares consecutivos como 2211 o 1122.');
          break;
        }
      }

      return problems;
    };

    const validate = () => {
      const problems = messages(password.value);
      error.textContent = problems.join(' ');
      password.setAttribute('aria-invalid', String(problems.length > 0 && password.value.length > 0));

      if (confirmation && confirmation.value && confirmation.value !== password.value) {
        confirmation.setCustomValidity('Las contraseñas no coinciden.');
      } else if (confirmation) {
        confirmation.setCustomValidity('');
      }

      return problems.length === 0 && (!confirmation || confirmation.value === password.value);
    };

    password.addEventListener('input', validate);
    confirmation?.addEventListener('input', validate);
    form.addEventListener('submit', event => {
      if (!validate() || !form.reportValidity()) {
        event.preventDefault();
        (form.querySelector(':invalid') || password).focus();
      }
    });
  });
});
