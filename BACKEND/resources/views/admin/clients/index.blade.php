@extends('layouts.app')
@section('title','Kelola Klien')
@section('content')
<h1>Kelola Klien / UMKM</h1>
<div class="table-wrap card"><table><thead><tr><th>Nama</th><th>Kontak</th><th>Status</th><th>Jumlah Pesanan</th><th></th></tr></thead><tbody>
@forelse($clients as $client)<tr><td>{{ $client->name }}<br><small>{{ $client->username }}</small></td><td>{{ $client->email }}<br><small>{{ $client->phone ?? '-' }}</small></td><td><span class="badge">{{ $client->account_status }}</span></td><td>{{ $client->client_orders_count }}</td><td><a href="{{ route('admin.clients.show',$client) }}">Detail</a></td></tr>@empty<tr><td colspan="5">Belum ada klien.</td></tr>@endforelse
</tbody></table></div>
@endsection
