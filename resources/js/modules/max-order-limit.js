import { showToast } from './toast';

/**
 * Batasi jumlah pembelian di sisi klien sesuai max_per_order produk.
 *
 * Markup: input number dengan data-max-per-order="<batas>".
 * Opsional: data-max-order-message untuk mengganti pesan notifikasi.
 */
export function initMaxOrderLimits() {
    document.querySelectorAll('input[data-max-per-order]').forEach((input) => {
        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        const max = Number(input.dataset.maxPerOrder);

        if (!Number.isFinite(max) || max < 1) {
            return;
        }

        const message =
            input.dataset.maxOrderMessage ||
            `Produk ini hanya dapat dipesan maksimal ${max} pcs per transaksi.`;

        const enforceLimit = () => {
            const value = Number(input.value);

            if (!Number.isFinite(value) || value <= max) {
                return false;
            }

            input.value = String(max);
            showToast(message);

            return true;
        };

        ['input', 'change', 'keyup'].forEach((eventName) => {
            input.addEventListener(eventName, enforceLimit);
        });

        input.form?.addEventListener('submit', (event) => {
            if (enforceLimit()) {
                event.preventDefault();
                input.focus();
            }
        });

        enforceLimit();
    });
}
