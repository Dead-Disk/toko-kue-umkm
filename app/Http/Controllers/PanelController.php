<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

abstract class PanelController extends Controller
{
    /**
     * Prefix panel yang dipakai controller: 'admin' atau 'staff'.
     */
    protected string $panel = 'admin';

    /**
     * Tampilkan view milik panel yang sedang aktif.
     *
     * @param  array<string, mixed>  $data
     */
    protected function panelView(string $view, array $data = []): View
    {
        return view($this->panel.'.'.$view, $data);
    }

    /**
     * Redirect ke route milik panel yang sedang aktif.
     *
     * @param  array<string, mixed>  $parameters
     */
    protected function panelRedirect(string $name, array $parameters = []): RedirectResponse
    {
        return redirect()->route($this->panel.'.'.$name, $parameters);
    }
}
