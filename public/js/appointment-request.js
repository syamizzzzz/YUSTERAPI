const form = document.querySelector('#appointment-request-form');

if (form) {
    const feedback = document.querySelector('#appointment-request-feedback');
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        feedback.replaceChildren();
        feedback.hidden = true;
        submitButton.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(form),
            });
            const result = await response.json();

            if (response.ok) {
                feedback.className = 'public-success';
                feedback.textContent = result.message;
                form.reset();
            } else {
                feedback.className = 'public-error';
                const messages = Object.values(result.errors ?? {}).flat();
                feedback.textContent = messages.join(' ') || result.message || 'Sila cuba lagi.';
            }

            feedback.hidden = false;
            feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } catch {
            feedback.className = 'public-error';
            feedback.textContent = 'Sambungan terganggu. Sila cuba lagi.';
            feedback.hidden = false;
        } finally {
            submitButton.disabled = false;
        }
    });
}
