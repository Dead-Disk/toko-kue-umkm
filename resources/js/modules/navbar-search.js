/**
 * Modul Pencarian Navbar Langsung
 *
 * Mengontrol animasi shape pencarian di navbar, auto-focus,
 * sinkronisasi live filter di halaman katalog, serta dropdown
 * saran pencarian kue secara instan via AJAX.
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
    const searchBar = document.getElementById('navbar-search-bar');
    const backdrop = document.getElementById('navbar-search-backdrop');
    const closeBtn = document.getElementById('navbar-search-close');
    const searchForm = document.getElementById('navbar-search-form');
    const searchInput = document.getElementById('navbar-search-input');
    const clearBtn = document.getElementById('navbar-search-clear-input');
    const dropdown = document.getElementById('navbar-search-dropdown');
    const loadingEl = document.getElementById('navbar-search-loading');
    const resultsEl = document.getElementById('navbar-search-results');
    const footerEl = document.getElementById('navbar-search-dropdown-footer');
    const countLabel = document.getElementById('navbar-search-count-label');

    if (!toggleBtn || !searchBar) {
        return;
    }

    let searchTimeout = null;
    let abortController = null;

    function openSearch() {
        searchBar.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-2');
        searchBar.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');

        if (backdrop) {
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
        }

        toggleBtn.setAttribute('aria-expanded', 'true');

        // Jika di halaman katalog ada query sebelumnya, samakan
        const catalogSearch = document.getElementById('catalog-search');
        if (catalogSearch && catalogSearch.value && !searchInput.value) {
            searchInput.value = catalogSearch.value;
        }

        updateClearButton();

        setTimeout(() => {
            if (searchInput) {
                searchInput.focus();
                if (searchInput.value) {
                    searchInput.select();
                }
            }
        }, 60);
    }

    function closeSearch() {
        searchBar.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
        searchBar.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');

        if (backdrop) {
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
        }

        if (dropdown) {
            dropdown.classList.add('hidden');
        }

        if (abortController) {
            abortController.abort();
            abortController = null;
        }

        toggleBtn.setAttribute('aria-expanded', 'false');
    }

    function updateClearButton() {
        if (!clearBtn || !searchInput) return;
        if (searchInput.value.trim().length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
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
        const actionUrl = searchForm ? searchForm.getAttribute('action') : '/produk';
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
                        <div class="p-4 text-center">
                            <p class="text-xs sm:text-sm text-gray-500">Tidak ada kue yang cocok dengan "<span class="font-semibold text-gray-700">${escapeHtml(query)}</span>"</p>
                            <p class="text-[11px] text-gray-400 mt-1">Coba kata kunci lain atau lihat seluruh koleksi kue kami.</p>
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
                            ? `<img src="${item.foto}" alt="${escapeHtml(item.nama_kue)}" class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl object-cover shrink-0 border border-[#F4E1D9]">`
                            : `<div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-[#FAF2EE] flex items-center justify-center shrink-0 text-[#6B0F1A] border border-[#F4E1D9]">
                                   <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M3 21h18M3 10h18M3 7l9-4 9 4v14H3V7z"/></svg>
                               </div>`;

                        return `
                            <a href="${item.url}" class="flex items-center gap-3 p-2.5 sm:p-3 hover:bg-[#FAF2EE]/80 transition group">
                                ${imageHtml}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-xs sm:text-sm text-gray-900 group-hover:text-[#6B0F1A] truncate">${escapeHtml(item.nama_kue)}</span>
                                        <span class="text-[9px] sm:text-[10px] px-1.5 py-0.5 rounded-full font-medium border ${statusClass} shrink-0">${statusText}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px] sm:text-xs text-gray-500 mt-0.5">
                                        <span class="font-semibold text-[#6B0F1A]">${item.formatted_harga}</span>
                                        ${item.category ? `<span class="truncate">• ${escapeHtml(item.category)}</span>` : ''}
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#6B0F1A] group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                            Terjadi kendala saat mencari. Silakan tekan tombol Cari.
                        </div>
                    `;
                }
            });
    }

    // Event Listeners
    toggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
        if (isExpanded) {
            closeSearch();
        } else {
            openSearch();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeSearch();
            toggleBtn.focus();
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            closeSearch();
        });
    }

    if (clearBtn && searchInput) {
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            updateClearButton();
            searchInput.focus();

            if (dropdown) {
                dropdown.classList.add('hidden');
            }

            // Sync ke catalog-search bila ada
            const catalogSearch = document.getElementById('catalog-search');
            if (catalogSearch) {
                catalogSearch.value = '';
                catalogSearch.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            updateClearButton();

            // Jika di halaman katalog kue (/produk), langsung filter live grid produk juga
            const catalogSearch = document.getElementById('catalog-search');
            if (catalogSearch && catalogSearch.value !== e.target.value) {
                catalogSearch.value = e.target.value;
                catalogSearch.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // Debounce AJAX auto-suggestions
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

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && toggleBtn.getAttribute('aria-expanded') === 'true') {
            closeSearch();
            toggleBtn.focus();
        }
    });
}
