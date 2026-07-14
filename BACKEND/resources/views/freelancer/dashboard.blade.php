@extends('layouts.app')
@section('title','Dashboard Freelancer')
@section('content')
<h1>Dashboard Freelancer</h1><div class="grid"><div class="card">Job sesuai skill<div class="stats">{{ $stats['available_jobs'] }}</div></div><div class="card">Tugas aktif<div class="stats">{{ $stats['active_tasks'] }}</div></div><div class="card">Tugas selesai<div class="stats">{{ $stats['completed_tasks'] }}</div></div><div class="card">Saldo tersedia<div class="stats">Rp{{ number_format($wallet->available_balance,0,',','.') }}</div></div></div>
<div class="card"><h2>Tugas Terbaru</h2>@forelse($tasks as $task)<p><a href="{{ route('freelancer.tasks.show',$task) }}">{{ $task->order_code }} — {{ $task->title }}</a> <span class="badge">{{ $task->status_label }}</span></p>@empty<p>Belum ada tugas.</p>@endforelse</div>
@endsection
