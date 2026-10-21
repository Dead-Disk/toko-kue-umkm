/**
 * Tampilkan detail pembayaran (rekening bank / gambar QRIS) sesuai metode
 * yang sedang dipilih. Detail yang tidak dipilih disembunyikan.
 *
 * Markup: elemen induk ber-atribut data-payment-group, berisi radio
 * name="metode_pembayaran" dan blok detail ber-atribut data-payment-detail="<value>".
 */
export function initPaymentDetails() {
    document.querySelectorAll('[data-payment-group]').forEach((group) => {
        const radios = group.querySelectorAll('input[name="metode_pembayaran"]');
        const details = group.querySelectorAll('[data-payment-detail]');

        const sync = () => {
            const selected = group.querySelector('input[name="metode_pembayaran"]:checked')?.value ?? null;

            details.forEach((detail) => {
                detail.hidden = detail.dataset.paymentDetail !== selected;
            });

            const proofContainer = group.querySelector('[data-payment-proof]');
            const proofInput = group.querySelector('[data-payment-proof-input]');

            if (proofContainer && proofInput) {
                const wajib = selected !== null && selected !== 'cash';

                proofContainer.hidden = !wajib;
                proofInput.required = wajib;
            }
        };

        radios.forEach((radio) => radio.addEventListener('change', sync));
        sync();
    });
}
