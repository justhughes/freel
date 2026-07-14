@extends('layouts.app')
@section('title','Paket Contify')
@section('content')
<h1>Layanan Konten untuk UMKM</h1>
<p>Pilih paket, isi brief, unggah aset, pilih jadwal, lalu pantau prosesnya sampai selesai.</p>
<div class="grid">
@forelse($packages as $package)
<div class="card">
    <span class="badge">{{ $package->category->name }}</span>
    <h2>{{ $package->name }}</h2>
    <p>{{ $package->description }}</p>
    <div class="price">Rp{{ number_format($package->base_price,0,',','.') }}</div>
    <p>{{ $package->regular_days }} hari • revisi {{ $package->revision_limit }}x • {{ $package->total_slot }} slot</p>
    <ul>@foreach($package->includes ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul>
    <a class="button" href="{{ route('client.orders.create',['package'=>$package->id]) }}">Pesan Paket</a>
</div>
@empty<div class="card">Belum ada paket aktif.</div>@endforelse
</div>
@endsection
