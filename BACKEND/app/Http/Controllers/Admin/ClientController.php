<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = User::query()
            ->where('role', User::ROLE_CLIENT)
            ->withCount('clientOrders')
            ->latest()
            ->get();

        return view('admin.clients.index', compact('clients'));
    }

    public function show(User $client): View
    {
        abort_unless($client->isClient(), 404);
        $client->load(['clientOrders' => fn ($query) => $query->with('package')->latest()]);

        return view('admin.clients.show', compact('client'));
    }

    public function toggleStatus(User $client): RedirectResponse
    {
        abort_unless($client->isClient(), 404);
        $client->update([
            'account_status' => $client->account_status === User::STATUS_ACTIVE
                ? User::STATUS_INACTIVE
                : User::STATUS_ACTIVE,
        ]);

        return back()->with('success', 'Status akun klien berhasil diperbarui.');
    }
}
