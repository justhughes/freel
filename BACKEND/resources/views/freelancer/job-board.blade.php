@extends('layouts.app')
@section('title','Job Board')
@section('content')
<h1>Job Board</h1><p>Hanya pekerjaan yang sesuai dengan keahlian aktif kamu yang ditampilkan.</p><div class="grid">@forelse($orders as $order)<div class="card"><span class="badge">{{ $order->package->category->name }}</span><h2>{{ $order->title }}</h2><p>{{ $order->package->name }} • {{ $order->client->name }}</p><p>Deadline {{ $order->deadline_at?->format('d M Y H:i') ?? '-' }} WIB</p><div class="price">Rp{{ number_format($order->freelancer_earning,0,',','.') }}</div><form method="POST" action="{{ route('freelancer.jobs.take',$order) }}">@csrf<button>Ambil Pekerjaan</button></form></div>@empty<div class="card">Belum ada pekerjaan yang sesuai.</div>@endforelse</div>
@endsection
