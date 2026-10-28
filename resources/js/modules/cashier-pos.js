const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
});

/**
 * Kelola baris item dinamis pada halaman kasir / POS offline.
 *
 * Markup: form[data-cashier-pos] dengan tbody[data-cashier-rows], satu
 * template[data-cashier-row-template], tombol tambah baris, dan elemen
 * total/kembalian. Setiap baris berisi select produk (data-harga & data-stok
 * pada option), input qty, subtotal, dan tombol hapus.
 */
export function initCashierPos() {
    document.querySelectorAll('form[data-cashier-pos]').forEach((form) => {
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const rows = form.querySelector('[data-cashier-rows]');
        const template = form.querySelector('template[data-cashier-row-template]');
        const addButton = form.querySelector('[data-cashier-add]');
        const paid = form.querySelector('[data-cashier-paid]');
        const total = form.querySelector('[data-cashier-total]');
        const change = form.querySelector('[data-cashier-change]');

        if (!(rows instanceof HTMLElement)) {
            return;
        }

        const format = (value) => rupiah.format(Number.isFinite(value) ? value : 0);

        const rowElements = () => [...rows.querySelectorAll('[data-cashier-row]')];

        let nextIndex = rowElements().length;

        const refreshChange = (grandTotal) => {
            if (!change) {
                return;
            }

            const value = paid instanceof HTMLInputElement ? paid.value.trim() : '';

            if (value === '') {
                change.textContent = '-';
                change.style.color = '';

                return;
            }

            const dibayar = Number(value);

            if (!Number.isFinite(dibayar) || dibayar < 0) {
                change.textContent = '-';
                change.style.color = '';

                return;
            }

            if (dibayar < grandTotal) {
                change.textContent = `Kurang ${format(grandTotal - dibayar)}`;
                change.style.color = '#b91c1c';

                return;
            }

            change.textContent = format(dibayar - grandTotal);
            change.style.color = '#166534';
        };

        const refreshRow = (row) => {
            const select = row.querySelector('[data-cashier-product]');
            const qtyInput = row.querySelector('[data-cashier-qty]');
            const price = row.querySelector('[data-cashier-price]');
            const stock = row.querySelector('[data-cashier-stock]');
            const subtotal = row.querySelector('[data-cashier-subtotal]');

            if (!(select instanceof HTMLSelectElement) || !(qtyInput instanceof HTMLInputElement)) {
                return 0;
            }

            const option = select.selectedOptions[0];
            const hasProduct = select.value !== '';
            const harga = Number(option?.dataset.harga ?? 0);
            const stok = Number(option?.dataset.stok ?? 0);

            if (price) {
                price.textContent = hasProduct ? format(harga) : '-';
            }

            if (stock) {
                stock.textContent = hasProduct ? `${stok} pcs` : '-';
            }

            if (hasProduct) {
                qtyInput.max = String(Math.max(1, stok));

                let value = Math.floor(Number(qtyInput.value));

                if (!Number.isFinite(value) || value < 1) {
                    value = 1;
                }

                if (value > stok) {
                    value = stok;
                }

                if (String(value) !== qtyInput.value) {
                    qtyInput.value = String(value);
                }
            } else {
                qtyInput.removeAttribute('max');
            }

            const qty = Math.max(1, Math.floor(Number(qtyInput.value)) || 1);
            const nilai = hasProduct ? harga * qty : 0;

            if (subtotal) {
                subtotal.textContent = format(nilai);
            }

            return nilai;
        };

        const recalc = () => {
            const grandTotal = rowElements().reduce((sum, row) => sum + refreshRow(row), 0);

            if (total) {
                total.textContent = format(grandTotal);
            }

            refreshChange(grandTotal);
        };

        const reindex = () => {
            const items = rowElements();

            items.forEach((row, index) => {
                row.querySelectorAll('[name]').forEach((input) => {
                    input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                });
            });

            nextIndex = items.length;
        };

        const addRow = () => {
            if (!(template instanceof HTMLTemplateElement)) {
                return;
            }

            const fragment = template.content.cloneNode(true);
            const row = fragment.querySelector('[data-cashier-row]');

            if (!row) {
                return;
            }

            row.querySelectorAll('[name]').forEach((input) => {
                input.name = input.name.replaceAll('__INDEX__', String(nextIndex));
            });

            nextIndex += 1;
            rows.appendChild(row);
            recalc();

            row.querySelector('[data-cashier-product]')?.focus();
        };

        if (addButton) {
            addButton.addEventListener('click', addRow);
        }

        rows.addEventListener('change', (event) => {
            const select = event.target.closest('[data-cashier-product]');

            if (!select) {
                return;
            }

            const qty = select.closest('[data-cashier-row]')?.querySelector('[data-cashier-qty]');

            if (qty instanceof HTMLInputElement) {
                qty.value = '1';
            }

            recalc();
        });

        rows.addEventListener('input', (event) => {
            if (event.target.closest('[data-cashier-qty]')) {
                recalc();
            }
        });

        rows.addEventListener('click', (event) => {
            const button = event.target.closest('[data-cashier-remove]');

            if (!button) {
                return;
            }

            const row = button.closest('[data-cashier-row]');

            if (!row) {
                return;
            }

            if (rowElements().length <= 1) {
                const select = row.querySelector('[data-cashier-product]');
                const qty = row.querySelector('[data-cashier-qty]');

                if (select instanceof HTMLSelectElement) {
                    select.value = '';
                }

                if (qty instanceof HTMLInputElement) {
                    qty.value = '1';
                }
            } else {
                row.remove();
                reindex();
            }

            recalc();
        });

        paid?.addEventListener('input', recalc);

        if (rowElements().length === 0) {
            addRow();
        } else {
            recalc();
        }
    });
}
