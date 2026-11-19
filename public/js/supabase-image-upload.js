document.addEventListener('DOMContentLoaded', () => {
    const signUrl = document.querySelector('meta[name="supabase-upload-sign-url"]')?.content
        ?? document.querySelector('[data-upload-sign-url]')?.value;
    if (!signUrl) return;

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        const failedUpload = [...form.elements]
            .filter((element) => element.matches('[data-supabase-image-upload]'))
            .find((input) => input.dataset.uploadState === 'failed' || (input.files?.length && input.dataset.uploadState !== 'complete'));
        if (!failedUpload) return;

        event.preventDefault();
        const status = document.getElementById(failedUpload.dataset.status);
        if (status) {
            status.textContent = 'Gambar belum berjaya dimuat naik. Sila tunggu sehingga status upload berjaya sebelum menyimpan.';
            status.dataset.state = 'error';
            status.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, true);

    document.querySelectorAll('[data-supabase-image-upload]').forEach((input) => {
        input.addEventListener('change', async () => {
            const file = input.files?.[0];
            if (!file) return;

            const status = document.getElementById(input.dataset.status);
            const pathField = input.form?.elements.namedItem(input.dataset.target);
            const submitButtons = [...(input.form?.querySelectorAll('button[type="submit"], button:not([type])') ?? [])];
            const oldButtonStates = submitButtons.map((button) => button.disabled);
            const showStatus = (message, state) => {
                if (!status) return;
                status.textContent = message;
                status.dataset.state = state;
            };

            input.dataset.uploadState = 'failed';
            if (!pathField) {
                showStatus('Borang gambar tidak lengkap. Muat semula halaman dan cuba lagi.', 'error');
                return;
            }
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                showStatus('Pilih gambar PNG, JPG atau WebP.', 'error');
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                showStatus('Saiz gambar mestilah 2 MB atau kurang.', 'error');
                return;
            }

            submitButtons.forEach((button) => { button.disabled = true; });
            input.disabled = true;
            input.dataset.uploadState = 'uploading';
            showStatus('Sedang menghantar gambar terus ke Supabase…', 'pending');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const signResponse = await fetch(signUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ context: input.dataset.context, mime_type: file.type, size: file.size }),
                });
                let signData;
                try {
                    signData = await signResponse.json();
                } catch {
                    throw new Error(`Pelayan Laravel memberi respons yang tidak dijangka (${signResponse.status}). Muat semula halaman dan log masuk semula.`);
                }
                if (!signResponse.ok) {
                    throw new Error(signData.message || Object.values(signData.errors ?? {}).flat()[0] || 'Laravel tidak dapat menyediakan pautan upload.');
                }

                let uploadResponse;
                try {
                    uploadResponse = await fetch(signData.upload_url, {
                        method: 'PUT',
                        headers: { 'Content-Type': file.type },
                        body: file,
                    });
                } catch {
                    throw new Error('Pelayar tidak dapat mencapai Supabase Storage. Semak sambungan internet atau tetapan CORS projek Supabase.');
                }
                if (!uploadResponse.ok) {
                    const responseText = await uploadResponse.text();
                    throw new Error(`Supabase menolak gambar (${uploadResponse.status}). ${responseText.slice(0, 180)}`);
                }

                pathField.value = signData.object_path;
                const removeName = input.dataset.removeTarget
                    || (input.dataset.context === 'package' ? 'remove_background_image' : `remove_${input.dataset.context}`);
                const removeCheckbox = input.form?.elements.namedItem(removeName);
                if (removeCheckbox?.checked) removeCheckbox.checked = false;
                input.dataset.uploadState = 'complete';
                showStatus(`Gambar berjaya dimuat naik: ${file.name}. Tekan Simpan untuk menggunakannya.`, 'success');
            } catch (error) {
                input.dataset.uploadState = 'failed';
                showStatus(error.message || 'Upload gagal. Sila cuba semula.', 'error');
            } finally {
                input.disabled = false;
                submitButtons.forEach((button, index) => { button.disabled = oldButtonStates[index]; });
            }
        });
    });
});
