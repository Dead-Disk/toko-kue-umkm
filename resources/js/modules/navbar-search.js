/**
 * Modul Pencarian Langsung di Main Content Navbar
 *
 * Mengontrol animasi shape input pencarian yang melebar halus dari sebelah kiri
 * tombol search, auto-focus, live-sync ke katalog kue, serta dropdown saran pencarian.
 */

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

export function initNavbarSearch() {
    const toggleBtn = document.getElementById('navbar-search-toggle');
    const searchShape = document.getElementById('navbar-search-shape');
    const navLinks = document.getElementById('navbar-nav-links');
    const searchForm = document.getElementById('navbar-search-form');
    const searchInput = document.getElementById('navbar-search-input');
    const closeBtn = document.getElementById('navbar-search-close');
    const dropdown = document.getElementById('navbar-search-dropdown');
    const loadingEl = document.getElementById('navbar-search-loading');
    const resultsEl = document.getElementById('navbar-search-results');
    const footerEl = document.getElementById('navbar-search-dropdown-footer');
    const countLabel = document.getElementById('navbar-search-count-label');

    if (!toggleBtn || !searchShape || !searchForm) {
        return;
    }

    let searchTimeout = null;
    let abortController = null;

    function isSearchOpen() {
        return toggleBtn.getAttribute('aria-expanded') === 'true';
    }

    function openSearch() {
        searchShape.classList.remove('max-w-0', 'opacity-0', 'pointer-events-none');
        searchShape.classList.add('max-w-xs', 'sm:max-w-sm', 'md:max-w-md', 'opacity-100', 'pointer-events-auto');

        if (navLinks) {
            navLinks.classList.add('max-md:max-w-0', 'max-md:opacity-0', 'max-md:overflow-hidden');
        }

        toggleBtn.setAttribute('aria-expanded', 'true');
        toggleBtn.setAttribute('title', 'Cari sekarang');

        // Jika di halaman katalog ada query sebelumnya, samakan
        const catalogSearch = document.getElementById('catalog-search');
        if (catalogSearch && catalogSearch.value && !searchInput.value) {
            searchInput.value = catalogSearch.value;
        }

        setTimeout(() => {
            if (searchInput) {
                searchInput.focus();
                if (searchInput.value) {
                    searchInput.select();
                }
            }
        }, 50);
    }

    function closeSearch() {
        searchShape.classList.remove('max-w-xs', 'sm:max-w-sm', 'md:max-w-md', 'opacity-100', 'pointer-events-auto');
        searchShape.classList.add('max-w-0', 'opacity-0', 'pointer-events-none');

        if (navLinks) {
            navLinks.classList.remove('max-md:max-w-0', 'max-md:opacity-0', 'max-md:overflow-hidden');
        }

        if (dropdown) {
            dropdown.classList.add('hidden');
        }

        if (abortController) {
            abortController.abort();
            abortController = null;
        }

        toggleBtn.setAttribute('aria-expanded', 'false');
        toggleBtn.setAttribute('title', 'Cari Kue');
    }

    function fetchSuggestions(query) {
        if (abortController) {
            abortController.abort();
        }

        if (!query || query.length === 0) {
            if (dropdown) dropdown.classList.add('hidden');
            return;
        }

        if (dropdown) {
            dropdown.classList.remove('hidden');
        }

        if (loadingEl) {
            loadingEl.classList.remove('hidden');
        }

        if (resultsEl) {
            resultsEl.innerHTML = '';
        }

        if (footerEl) {
            footerEl.classList.add('hidden');
        }

        abortController = new AbortController();
        const actionUrl = searchForm.getAttribute('action') || '/produk';
        const url = new URL(actionUrl, window.location.origin);
        url.searchParams.set('search', query);

        fetch(url.toString(), {
            signal: abortController.signal,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((res) => {
                if (!res.ok) throw new Error('Gagal memuat hasil');
                return res.json();
            })
            .then((data) => {
                if (loadingEl) {
                    loadingEl.classList.add('hidden');
                }

                const items = data.data || [];
                const total = data.total || items.length;

                if (!resultsEl) return;

                if (items.length === 0) {
                    resultsEl.innerHTML = `
                        <div class="p-3.5 text-center">
                            <p class="text-xs sm:text-sm text-gray-500">Tidak ada kue yang cocok dengan "<span class="font-semibold text-gray-700">${escapeHtml(query)}</span>"</p>
                            <p class="text-[11px] text-gray-400 mt-1">Coba kata kunci lain atau buka katalog lengkap.</p>
                        </div>
                    `;
                    if (footerEl) {
                        footerEl.classList.remove('hidden');
                        if (countLabel) countLabel.textContent = '0 kue';
                    }
                    return;
                }

                resultsEl.innerHTML = items
                    .map((item) => {
                        const statusClass = item.is_ready
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                            : 'bg-rose-50 text-rose-700 border-rose-200';
                        const statusText = item.is_ready ? `Ready (${item.stok})` : 'Habis';

                        const imageHtml = item.foto
                            ? `<img src="${item.foto}" alt="${escapeHtml(item.nama_kue)}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl object-cover shrink-0 border border-[#F4E1D9]">`
                            : `<div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#FAF2EE] flex items-center justify-center shrink-0 text-[#6B0F1A] border border-[#F4E1D9]">
                                   <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M3 21h18M3 10h18M3 7l9-4 9 4v14H3V7z"/></svg>
                               </div>`;

                        return `
                            <a href="${item.url}" class="flex items-center gap-2.5 p-2 sm:p-2.5 hover:bg-[#FAF2EE]/80 transition group">
                                ${imageHtml}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-semibold text-xs sm:text-sm text-gray-900 group-hover:text-[#6B0F1A] truncate">${escapeHtml(item.nama_kue)}</span>
                                        <span class="text-[9px] px-1.5 py-0.2 rounded-full font-medium border ${statusClass} shrink-0">${statusText}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] text-gray-500 mt-0.5">
                                        <span class="font-semibold text-[#6B0F1A]">${item.formatted_harga}</span>
                                        ${item.category ? `<span class="truncate">• ${escapeHtml(item.category)}</span>` : ''}
                                    </div>
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-[#6B0F1A] group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        `;
                    })
                    .join('');

                if (footerEl) {
                    footerEl.classList.remove('hidden');
                    if (countLabel) {
                        countLabel.textContent = total > items.length
                            ? `${items.length} dari ${total} kue ditemukan`
                            : `${total} kue ditemukan`;
                    }
                }
            })
            .catch((err) => {
                if (err.name === 'AbortError') return;
                if (loadingEl) loadingEl.classList.add('hidden');
                if (resultsEl) {
                    resultsEl.innerHTML = `
                        <div class="p-3 text-center text-xs text-rose-600">
                            Terjadi kendala saat mencari. Silakan tekan Enter untuk mencari langsung di katalog.
                        </div>
                    `;
                }
            });
    }

    // Toggle button click: jika belum buka, buka shape. Jika sudah buka dan ada query, submit form. Jika kosong, tutup shape.
    toggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (!isSearchOpen()) {
            openSearch();
        } else {
            const query = searchInput ? searchInput.value.trim() : '';
            if (query.length > 0) {
                searchForm.submit();
            } else {
                closeSearch();
            }
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (searchInput) {
                searchInput.value = '';
            }
            closeSearch();
            toggleBtn.focus();

            // Clear sync di katalog jika ada
            const catalogSearch = document.getElementById('catalog-search');
            if (catalogSearch && catalogSearch.value) {
                catalogSearch.value = '';
                catalogSearch.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();

            // Jika di halaman katalog kue (/produk), live sync filter produk grid
            const catalogSearch = document.getElementById('catalog-search');
            if (catalogSearch && catalogSearch.value !== e.target.value) {
                catalogSearch.value = e.target.value;
                catalogSearch.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // Debounce live suggestions
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchSuggestions(query);
            }, 250);
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                e.preventDefault();
                closeSearch();
                toggleBtn.focus();
            }
        });
    }

    // Klik di luar search form akan menutup shape
    document.addEventListener('click', (e) => {
        if (!isSearchOpen()) return;
        if (!searchForm.contains(e.target)) {
            closeSearch();
        }
    });

    // Escape keyboard shortcut
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isSearchOpen()) {
            closeSearch();
            toggleBtn.focus();
        }
    });
}
