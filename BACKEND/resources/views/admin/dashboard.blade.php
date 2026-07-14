@extends('layouts.app')
@section('title','Dashboard Admin')
@section('content')
<h1>Dashboard Admin</h1><div class="grid">@foreach($stats as $key=>$value)<div class="card"><small>{{ ucwords(str_replace('_',' ',$key)) }}</small><div class="stats">{{ str_contains($key,'revenue') ? 'Rp'.number_format($value,0,',','.') : number_format($value) }}</div></div>@endforeach</div>
<div class="card"><h2>Pesanan Terbaru</h2><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Klien</th><th>Paket</th><th>Freelancer</th><th>Status</th></tr></thead><tbody>@foreach($latestOrders as $order)<tr><td><a href="{{ route('admin.orders.show',$order) }}">{{ $order->order_code }}</a></td><td>{{ $order->client->name }}</td><td>{{ $order->package->name }}</td><td>{{ $order->freelancer?->name ?? '-' }}</td><td>{{ $order->status_label }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
