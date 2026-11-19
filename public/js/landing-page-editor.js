document.addEventListener('DOMContentLoaded', () => {
    const list = document.querySelector('#custom-fields-list');
    const template = document.querySelector('#custom-field-template');
    const addButton = document.querySelector('#add-custom-field');

    const reindex = () => {
        [...list.querySelectorAll('.custom-field-card')].forEach((card, index) => {
            card.querySelectorAll('[data-field]').forEach((input) => {
                input.name = `form_fields[${index}][${input.dataset.field}]`;
            });
            const key = card.querySelector('[data-field="key"]');
            if (!key.value) {
                key.value = `field_${Date.now()}_${index}`;
            }
        });
        addButton.disabled = list.querySelectorAll('.custom-field-card').length >= 15;
    };

    const updateOptions = (card) => {
        const options = card.querySelector('.field-options');
        options.classList.toggle('hidden', card.querySelector('.custom-field-type').value !== 'select');
    };

    list.addEventListener('click', (event) => {
        if (event.target.closest('.remove-custom-field')) {
            event.target.closest('.custom-field-card').remove();
            reindex();
        }
    });
    list.addEventListener('change', (event) => {
        if (event.target.matches('.custom-field-type')) {
            updateOptions(event.target.closest('.custom-field-card'));
        }
    });
    addButton.addEventListener('click', () => {
        if (list.querySelectorAll('.custom-field-card').length >= 15) return;
        const card = template.content.firstElementChild.cloneNode(true);
        list.append(card);
        reindex();
        card.querySelector('[data-field="label"]').focus();
    });

    document.querySelectorAll('.custom-field-card').forEach(updateOptions);
    reindex();

    document.querySelectorAll('.color-input input[type="color"]').forEach((picker) => {
        picker.addEventListener('input', () => {
            picker.parentElement.querySelector('.hex-value').value = picker.value.toUpperCase();
        });
    });

    document.querySelectorAll('[data-overlay-range]').forEach((range) => {
        const output = range.parentElement.querySelector('[data-overlay-value]');
        const updateOpacity = () => { output.value = `${range.value}%`; output.textContent = `${range.value}%`; };
        range.addEventListener('input', updateOpacity);
        updateOpacity();
    });
});
