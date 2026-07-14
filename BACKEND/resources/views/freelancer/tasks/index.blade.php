@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')
    <h1>My Tasks</h1>

    <div class="table-wrap card">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Klien</th>
                    <th>Paket</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>{{ $order->client->name }}</td>
                        <td>{{ $order->package->name }}</td>
                        <td><span class="badge">{{ $order->status_label }}</span></td>
                        <td>{{ $order->deadline_at?->format('d M Y H:i') ?? '-' }} WIB</td>
                        <td><a href="{{ route('freelancer.tasks.show', $order) }}">Buka</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">Belum ada tugas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
