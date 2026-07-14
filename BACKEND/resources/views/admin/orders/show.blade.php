@extends('layouts.app')

@section('title', 'Detail Pesanan Admin')

@section('content')
    <div class="row">
        <div>
            <h1>{{ $order->order_code }}</h1>
            <p>{{ $order->title }}</p>
        </div>
        <span class="badge">{{ $order->status_label }}</span>
    </div>

    <div class="grid">
        <div class="card">
            <h3>Pesanan</h3>
            <p><strong>Dibuat:</strong> {{ $order->created_at->format('d M Y H:i') }} WIB</p>
            <p><strong>Klien:</strong> {{ $order->client->name }}</p>
            <p><strong>Paket:</strong> {{ $order->package->name }}</p>
            <p><strong>Freelancer:</strong> {{ $order->freelancer?->name ?? '-' }}</p>
            <p><strong>Deadline:</strong> {{ $order->deadline_at?->format('d M Y H:i') ?? '-' }} WIB</p>
            <p><strong>Revisi:</strong> {{ $order->revision_used }}/{{ $order->revision_limit }}</p>
        </div>

        <div class="card">
            <h3>Keuangan</h3>
            <p>Total: Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
            <p>Fee freelancer: Rp{{ number_format($order->freelancer_earning, 0, ',', '.') }}</p>
            <p>Margin: Rp{{ number_format($order->platform_margin, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="card">
        <h3>Brief</h3>
        <p><strong>Brand:</strong> {{ $order->business_name }}</p>
        <p><strong>Produk:</strong> {{ $order->product_description }}</p>
        <p><strong>Target:</strong> {{ $order->target_audience ?? '-' }}</p>
        <p><strong>Referensi visual:</strong> {{ $order->visual_reference ?? '-' }}</p>
        <p><strong>Catatan:</strong> {{ $order->brief ?? '-' }}</p>
    </div>


    <div class="card">
        <h3>Aset Mentah Klien</h3>
        @forelse($order->assets as $asset)
            <p>
                <a href="{{ route('admin.orders.assets.download', [$order, $asset]) }}">
                    Download {{ $asset->original_name }}
                </a>
            </p>
        @empty
            <p>Tidak ada aset mentah.</p>
        @endforelse
    </div>

    <div class="card">
        <h3>Riwayat Pembayaran</h3>
        @forelse($order->payments as $payment)
            <p>
                {{ $payment->payment_code }}
                — {{ $payment->method->name }} / {{ $payment->channel->name }}
                — {{ $payment->status }}
                @if($payment->paid_at)
                    — {{ $payment->paid_at->format('d M Y H:i') }} WIB
                @endif
            </p>
        @empty
            <p>Belum ada pembayaran.</p>
        @endforelse
    </div>

    <div class="card">
        <h3>Riwayat Submission</h3>
        @forelse($order->submissions as $submission)
            <details @if($loop->first) open @endif>
                <summary>
                    Versi {{ $submission->version }}
                    — {{ $submission->submission_type }}
                    — {{ $submission->submitted_at->format('d M Y H:i') }} WIB
                </summary>
                <p>{{ $submission->notes ?: 'Tanpa catatan.' }}</p>
                @foreach($submission->files as $file)
                    <p>
                        <a href="{{ route('admin.orders.results.download', [$order, $file]) }}">
                            Download {{ $file->original_name }}
                        </a>
                    </p>
                @endforeach
            </details>
        @empty
            <p>Belum ada hasil freelancer.</p>
        @endforelse
    </div>

    <div class="card">
        <h3>Riwayat Revisi</h3>
        @forelse($order->revisions as $revision)
            <details @if($loop->first) open @endif>
                <summary>
                    Pengajuan #{{ $revision->revision_number }}
                    @if($revision->approved_revision_number)
                        — revisi ke-{{ $revision->approved_revision_number }}
                    @endif
                    — {{ $revision->status_label }}
                </summary>
                <p><strong>Klien:</strong> {{ $revision->notes }}</p>
                @if($revision->admin_notes)
                    <p><strong>Admin:</strong> {{ $revision->admin_notes }}</p>
                @endif
                @if($revision->resultSubmission)
                    <p>Dijawab melalui hasil versi {{ $revision->resultSubmission->version }}.</p>
                @endif
            </details>
        @empty
            <p>Belum ada revisi.</p>
        @endforelse
    </div>
@endsection
