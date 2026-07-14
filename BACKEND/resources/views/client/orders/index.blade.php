@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
    <div class="row">
        <div>
            <h1>Pesanan Saya</h1>
            <p>Pantau seluruh proses tanpa perlu terus menghubungi admin.</p>
        </div>
        <a class="button" href="{{ route('client.orders.create') }}">Buat Pesanan</a>
    </div>

    <div class="table-wrap card">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Paket</th>
                    <th>Dibuat</th>
                    <th>Status</th>
                    <th>Freelancer</th>
                    <th>Total</th>
                    <th>Deadline</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>{{ $order->package->name }}</td>
                        <td>{{ $order->created_at->format('d M Y H:i') }} WIB</td>
                        <td><span class="badge">{{ $order->status_label }}</span></td>
                        <td>{{ $order->freelancer?->name ?? '-' }}</td>
                        <td>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>{{ $order->deadline_at?->format('d M Y H:i') ?? '-' }} WIB</td>
                        <td><a href="{{ route('client.orders.show', $order) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
