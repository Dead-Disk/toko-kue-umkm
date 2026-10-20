/**
 * Konfirmasi sebelum submit form destruktif (hapus, kosongkan, dsb).
 *
 * Pakai atribut data-confirm pada <form>, mis.:
 *   <form action="..." method="POST" data-confirm="Hapus produk ini?">
 *
 * Listener dipasang pada document (event delegation), jadi form yang
 * dirender belakangan tetap ikut terkonfirmasi.
 */
export function initConfirmActions() {
    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) {
            return;
        }

        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
}
