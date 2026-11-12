import { showToast } from './toast';

/**
 * Kelola perubahan stok dan status ketersediaan produk langsung dari tabel
 * stok panel staf, tanpa reload halaman.
 *
 * Markup: elemen [data-stock-cell] dengan data-update-url, data-stock, dan
 * data-is-ready. Di dalamnya ada tombol [data-stock-decrement]/[data-stock-
 * increment], input [data-stock-input], serta switch [data-stock-toggle]
 * dengan track, knob, dan label status.
 */
export function initStaffProductStock() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    document.querySelectorAll('[data-stock-cell]').forEach((cell) => {
        if (!(cell instanceof HTMLElement)) {
            return;
        }

        const updateUrl = cell.dataset.updateUrl;
        const input = cell.querySelector('[data-stock-input]');
        const decrement = cell.querySelector('[data-stock-decrement]');
        const increment = cell.querySelector('[data-stock-increment]');
        const toggle = cell.querySelector('[data-stock-toggle]');
        const track = cell.querySelector('[data-stock-toggle-track]');
        const knob = cell.querySelector('[data-stock-toggle-knob]');
        const label = cell.querySelector('[data-stock-status-label]');

        if (!updateUrl || !(input instanceof HTMLInputElement)) {
            return;
        }

        let busy = false;

        const currentStock = () => Math.max(0, Math.floor(Number(cell.dataset.stock) || 0));
        const isReady = () => cell.dataset.isReady === 'true';

        const syncControls = () => {
            const stock = currentStock();

            if (decrement instanceof HTMLButtonElement) {
                decrement.disabled = busy || stock <= 0;
            }

            if (increment instanceof HTMLButtonElement) {
                increment.disabled = busy;
            }

            input.disabled = busy;

            if (toggle instanceof HTMLButtonElement) {
                toggle.disabled = busy || stock <= 0;
                toggle.title = stock <= 0
                    ? 'Tambah stok terlebih dahulu untuk mengubah status'
                    : `Klik untuk menandai ${isReady() ? 'habis' : 'ready'}`;
            }
        };

        const setBusy = (state) => {
            busy = state;
            cell.setAttribute('aria-busy', state ? 'true' : 'false');
            syncControls();
        };

        const applyState = (product) => {
            const stock = Math.max(0, Math.floor(Number(product.stok) || 0));
            const ready = Boolean(product.is_ready);

            cell.dataset.stock = String(stock);
            cell.dataset.isReady = ready ? 'true' : 'false';
            input.value = String(stock);

            if (track instanceof HTMLElement) {
                track.classList.toggle('bg-emerald-500', ready);
                track.classList.toggle('bg-stone-300', !ready);
            }

            if (knob instanceof HTMLElement) {
                knob.classList.toggle('translate-x-3.5', ready);
                knob.classList.toggle('translate-x-0.5', !ready);
            }

            if (toggle instanceof HTMLButtonElement) {
                toggle.setAttribute('aria-checked', ready ? 'true' : 'false');
            }

            if (label instanceof HTMLElement) {
                label.textContent = ready ? 'Ready' : 'Habis';
                label.classList.toggle('text-emerald-700', ready);
                label.classList.toggle('text-stone-500', !ready);
            }

            syncControls();
        };

        const errorMessage = (payload) => {
            if (payload && typeof payload === 'object' && payload.errors && typeof payload.errors === 'object') {
                const messages = Object.values(payload.errors).flat();

                if (messages.length > 0) {
                    return String(messages[0]);
                }
            }

            return typeof payload?.message === 'string'
                ? payload.message
                : 'Gagal memperbarui stok produk.';
        };

        const update = async (payload) => {
            if (busy) {
                return;
            }

            setBusy(true);

            try {
                const response = await fetch(updateUrl, {
                    method: 'PATCH',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json().catch(() => null);

                if (!response.ok || !data?.product) {
                    throw new Error(errorMessage(data));
                }

                applyState(data.product);
            } catch (error) {
                showToast(error instanceof Error ? error.message : 'Gagal memperbarui stok produk.');
                input.value = String(currentStock());
            } finally {
                setBusy(false);
            }
        };

        decrement?.addEventListener('click', () => update({ delta: -1 }));
        increment?.addEventListener('click', () => update({ delta: 1 }));

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                input.blur();
            }
        });

        input.addEventListener('change', () => {
            const raw = input.value.trim();

            if (raw === '') {
                input.value = String(currentStock());

                return;
            }

            const value = Math.floor(Number(raw));

            if (!Number.isFinite(value) || value < 0) {
                input.value = String(currentStock());
                showToast('Jumlah stok harus berupa angka 0 atau lebih.');

                return;
            }

            if (value === currentStock()) {
                input.value = String(currentStock());

                return;
            }

            update({ stok: value });
        });

        toggle?.addEventListener('click', () => update({
            status_stok: isReady() ? 'habis' : 'ready',
        }));

        syncControls();
    });
}
