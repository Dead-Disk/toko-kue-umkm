const TOAST_DURATION = 5000;

/**
 * Tampilkan notifikasi singkat (toast) di pojok kanan atas.
 *
 * Toast dibuat lewat JavaScript dan memakai inline style agar tetap tampil
 * di halaman yang tidak memuat stylesheet aplikasi.
 */
export function showToast(message, type = 'error') {
    if (!message) {
        return null;
    }

    const toast = document.createElement('div');
    const text = document.createElement('span');
    const close = document.createElement('button');

    toast.dataset.toast = '';
    toast.dataset.type = type;
    toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
    toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
    toast.style.cssText = [
        'display:flex',
        'align-items:flex-start',
        'gap:.75rem',
        'pointer-events:auto',
        `background:${type === 'error' ? '#b91c1c' : '#166534'}`,
        'color:#fff',
        'padding:.75rem 1rem',
        'border-radius:.5rem',
        'box-shadow:0 10px 25px rgba(0,0,0,.18)',
        'font:14px/1.45 system-ui,sans-serif',
        'max-width:24rem',
        'opacity:0',
        'transform:translateY(-.5rem)',
        'transition:opacity .2s ease, transform .2s ease',
    ].join(';');

    text.textContent = message;

    close.type = 'button';
    close.setAttribute('aria-label', 'Tutup notifikasi');
    close.textContent = '×';
    close.style.cssText = [
        'background:transparent',
        'border:0',
        'color:inherit',
        'cursor:pointer',
        'font-size:1.15rem',
        'line-height:1',
        'padding:0',
    ].join(';');

    toast.append(text, close);
    getContainer().appendChild(toast);

    const remove = () => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-.5rem)';

        window.setTimeout(() => toast.remove(), 200);
    };

    close.addEventListener('click', remove);
    window.setTimeout(remove, TOAST_DURATION);

    window.requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });

    return toast;
}

/**
 * Tampilkan toast dari pesan flash server yang dirender ke halaman.
 *
 * Markup: <div data-toast-message="Pesan" data-toast-type="error" hidden></div>
 */
export function initToasts() {
    document.querySelectorAll('[data-toast-message]').forEach((element) => {
        showToast(element.dataset.toastMessage, element.dataset.toastType || 'error');
        element.remove();
    });
}

function getContainer() {
    let container = document.querySelector('[data-toast-container]');

    if (container) {
        return container;
    }

    container = document.createElement('div');
    container.dataset.toastContainer = '';
    container.style.cssText = [
        'position:fixed',
        'top:1rem',
        'right:1rem',
        'z-index:9999',
        'display:flex',
        'flex-direction:column',
        'align-items:flex-end',
        'gap:.5rem',
        'pointer-events:none',
    ].join(';');
    document.body.appendChild(container);

    return container;
}
