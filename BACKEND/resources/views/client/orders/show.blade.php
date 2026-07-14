@extends('layouts.app')

@section('title', 'Detail Pesanan')

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
            <h3>Informasi Pesanan</h3>
            <p><strong>Dibuat:</strong> {{ $order->created_at->format('d M Y H:i') }} WIB</p>
            <p><strong>Paket:</strong> {{ $order->package->name }}</p>
            <p><strong>Freelancer:</strong> {{ $order->freelancer?->name ?? 'Belum diambil' }}</p>
            <p><strong>Deadline:</strong> {{ $order->deadline_at?->format('d M Y H:i') ?? '-' }} WIB</p>
            <p>
                <strong>Revisi terpakai:</strong>
                {{ $order->revision_used }}/{{ $order->revision_limit }}
                — tersisa {{ $order->remaining_revisions }}
            </p>
        </div>

        <div class="card">
            <h3>Biaya</h3>
            <p>Subtotal: Rp{{ number_format($order->subtotal, 0, ',', '.') }}</p>
            <p>Diskon: Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</p>
            <div class="price">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</div>
        </div>
    </div>

    @if($order->status === \App\Models\Order::STATUS_PENDING_PAYMENT)
        <div class="card">
            <h3>Pembayaran</h3>
            <p>
                Backend ini belum menambah API payment gateway. Tombol berikut hanya
                digunakan untuk menguji alur backend web.
            </p>
            <form method="POST" action="{{ route('client.orders.simulate-payment', $order) }}">
                @csrf
                <button class="success">Simulasikan Pembayaran Berhasil</button>
            </form>
        </div>
    @endif

    @if($order->status === \App\Models\Order::STATUS_REVISION_REQUESTED)
        <div class="card">
            <h3>Pengajuan Revisi Sedang Diperiksa</h3>
            <p>
                Catatan revisi sudah masuk ke admin. Setelah disetujui, admin akan
                meneruskannya kepada freelancer.
            </p>
        </div>
    @endif

    @if($order->status === \App\Models\Order::STATUS_REVISION && $order->activeRevision)
        <div class="card">
            <h3>Revisi Sedang Dikerjakan</h3>
            <p>
                Revisi ke-{{ $order->activeRevision->approved_revision_number }}
                dari maksimal {{ $order->revision_limit }} revisi sudah diteruskan ke freelancer.
            </p>
            <p><strong>Catatan kamu:</strong> {{ $order->activeRevision->notes }}</p>
            @if($order->activeRevision->admin_notes)
                <p><strong>Catatan admin:</strong> {{ $order->activeRevision->admin_notes }}</p>
            @endif
        </div>
    @endif

    <div class="card">
        <h3>Brief</h3>
        <p><strong>Brand:</strong> {{ $order->business_name }}</p>
        <p><strong>Produk:</strong> {{ $order->product_description }}</p>
        <p><strong>Target:</strong> {{ $order->target_audience ?? '-' }}</p>
        <p><strong>Referensi visual:</strong> {{ $order->visual_reference ?? '-' }}</p>
        <p><strong>Catatan:</strong> {{ $order->brief ?? '-' }}</p>

        <h3>Aset Mentah</h3>
        @forelse($order->assets as $asset)
            <p>
                <a href="{{ route('client.orders.assets.download', [$order, $asset]) }}">
                    Download {{ $asset->original_name }}
                </a>
            </p>
        @empty
            <p>Tanpa file aset.</p>
        @endforelse
    </div>

    @if($order->currentSubmission)
        <div class="card">
            <div class="row">
                <div>
                    <h3>Hasil Terbaru dari Freelancer</h3>
                    <p>
                        Versi {{ $order->currentSubmission->version }}
                        — dikirim {{ $order->currentSubmission->submitted_at->format('d M Y H:i') }} WIB
                    </p>
                </div>
                <span class="badge">{{ ucfirst($order->currentSubmission->submission_type) }}</span>
            </div>

            <p>{{ $order->currentSubmission->notes ?: 'Tidak ada catatan tambahan.' }}</p>

            @forelse($order->currentSubmission->files as $file)
                <p>
                    <a href="{{ route('client.orders.results.download', [$order, $file]) }}">
                        Download {{ $file->original_name }}
                    </a>
                </p>
            @empty
                <p>File hasil belum tersedia.</p>
            @endforelse

            @if($order->status === \App\Models\Order::STATUS_REVIEW)
                <div class="actions">
                    <form method="POST" action="{{ route('client.orders.approve', $order) }}">
                        @csrf
                        <button class="success">Terima Hasil</button>
                    </form>

                    @if($order->canRequestRevision())
                        <details>
                            <summary>Minta Revisi</summary>
                            <form method="POST" action="{{ route('client.orders.revision', $order) }}">
                                @csrf
                                <label>Jelaskan bagian yang masih kurang</label>
                                <textarea
                                    name="notes"
                                    maxlength="5000"
                                    placeholder="Contoh: warna produk masih terlalu gelap, logo diperbesar, dan teks pembuka diperbaiki."
                                    required
                                >{{ old('notes') }}</textarea>
                                <button class="warning">Kirim Pengajuan Revisi</button>
                            </form>
                        </details>
                    @elseif($order->remaining_revisions <= 0)
                        <p>Batas revisi paket sudah habis.</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if($order->submissions->isNotEmpty())
        <div class="card">
            <h3>Riwayat Semua Hasil</h3>

            @foreach($order->submissions as $submission)
                <details @if($loop->first) open @endif>
                    <summary>
                        Versi {{ $submission->version }}
                        — {{ ucfirst($submission->submission_type) }}
                        — {{ $submission->submitted_at->format('d M Y H:i') }} WIB
                        @if($submission->id === $order->currentSubmission?->id)
                            (terbaru)
                        @endif
                    </summary>

                    <p>{{ $submission->notes ?: 'Tidak ada catatan tambahan.' }}</p>

                    @foreach($submission->files as $file)
                        <p>
                            <a href="{{ route('client.orders.results.download', [$order, $file]) }}">
                                Download {{ $file->original_name }}
                            </a>
                        </p>
                    @endforeach
                </details>
            @endforeach
        </div>
    @endif

    @if($order->revisions->isNotEmpty())
        <div class="card">
            <h3>Riwayat Revisi</h3>

            @foreach($order->revisions as $revision)
                <details @if($loop->first) open @endif>
                    <summary>
                        Pengajuan #{{ $revision->revision_number }}
                        @if($revision->approved_revision_number)
                            — revisi ke-{{ $revision->approved_revision_number }}
                        @endif
                        — {{ $revision->status_label }}
                    </summary>

                    <p>
                        <strong>Diajukan:</strong>
                        {{ $revision->requested_at->format('d M Y H:i') }} WIB
                    </p>
                    <p><strong>Catatan klien:</strong> {{ $revision->notes }}</p>

                    @if($revision->admin_notes)
                        <p><strong>Catatan admin:</strong> {{ $revision->admin_notes }}</p>
                    @endif

                    @if($revision->resultSubmission)
                        <p>
                            <strong>Jawaban freelancer:</strong>
                            hasil versi {{ $revision->resultSubmission->version }},
                            dikirim {{ $revision->resultSubmission->submitted_at->format('d M Y H:i') }} WIB
                        </p>

                        @foreach($revision->resultSubmission->files as $file)
                            <p>
                                <a href="{{ route('client.orders.results.download', [$order, $file]) }}">
                                    Download {{ $file->original_name }}
                                </a>
                            </p>
                        @endforeach
                    @endif
                </details>
            @endforeach
        </div>
    @endif
@endsection
