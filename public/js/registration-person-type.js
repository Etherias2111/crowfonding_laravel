document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[data-account-registration]').forEach(form => {
    const selector = form.querySelector('[data-person-type]');
    const groups = form.querySelectorAll('[data-person-fields]');
    if (!selector || groups.length === 0) return;

    const updateGroups = () => {
      groups.forEach(group => {
        const active = group.dataset.personFields === selector.value;
        group.hidden = !active;
        group.disabled = !active;
      });
    };

    selector.addEventListener('change', updateGroups);
    updateGroups();
  });
});
