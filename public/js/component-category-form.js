document.addEventListener('DOMContentLoaded', () => {
    const type = document.getElementById('product-category');
    const wrapper = document.getElementById('component-category-field');
    const category = document.getElementById('component-category-id');
    if (!type || !wrapper || !category) return;

    const sync = () => {
        const isComponent = type.value === 'component';
        wrapper.hidden = !isComponent;
        category.required = isComponent;
        if (!isComponent) category.value = '';
    };

    type.addEventListener('change', sync);
    sync();
});
