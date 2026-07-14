@extends('layouts.app')
@section('title','Detail Klien')
@section('content')
<h1>{{ $client->name }}</h1>
<div class="card"><p>Username: {{ $client->username }}</p><p>Email: {{ $client->email }}</p><p>Telepon: {{ $client->phone ?? '-' }}</p><p>Status: {{ $client->account_status }}</p><form method="POST" action="{{ route('admin.clients.status',$client) }}">@csrf @method('PATCH')<button class="warning">{{ $client->account_status==='active'?'Nonaktifkan':'Aktifkan' }} Akun</button></form></div>
<div class="card"><h2>Riwayat Pesanan</h2><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Paket</th><th>Status</th><th>Total</th></tr></thead><tbody>@forelse($client->clientOrders as $order)<tr><td><a href="{{ route('admin.orders.show',$order) }}">{{ $order->order_code }}</a></td><td>{{ $order->package->name }}</td><td>{{ $order->status_label }}</td><td>Rp{{ number_format($order->total_amount,0,',','.') }}</td></tr>@empty<tr><td colspan="4">Belum ada pesanan.</td></tr>@endforelse</tbody></table></div></div>
@endsection
