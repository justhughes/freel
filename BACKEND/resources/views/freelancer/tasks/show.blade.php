@extends('layouts.app')

@section('title', 'Detail Tugas')

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
            <h3>Klien</h3>
            <p>{{ $order->client->name }}</p>
            <p>{{ $order->package->name }}</p>
            <p>Dibuat: {{ $order->created_at->format('d M Y H:i') }} WIB</p>
            <p>Deadline: {{ $order->deadline_at?->format('d M Y H:i') ?? '-' }} WIB</p>
        </div>

        <div class="card">
            <h3>Bayaran</h3>
            <div class="price">Rp{{ number_format($order->freelancer_earning, 0, ',', '.') }}</div>
            <p>Diterima setelah klien menyetujui hasil.</p>
        </div>
    </div>

    <div class="card">
        <h3>Brief</h3>
        <p><strong>Brand:</strong> {{ $order->business_name }}</p>
        <p><strong>Produk:</strong> {{ $order->product_description }}</p>
        <p><strong>Target:</strong> {{ $order->target_audience ?? '-' }}</p>
        <p><strong>Referensi visual:</strong> {{ $order->visual_reference ?? '-' }}</p>
        <p><strong>Catatan:</strong> {{ $order->brief ?? '-' }}</p>

        <h3>Aset</h3>
        @forelse($order->assets as $asset)
            <p>
                <a href="{{ route('freelancer.tasks.assets.download', [$order, $asset]) }}">
                    Download {{ $asset->original_name }}
                </a>
            </p>
        @empty
            <p>Tidak ada aset.</p>
        @endforelse
    </div>

    @if($order->status === \App\Models\Order::STATUS_REVISION && $order->activeRevision)
        <div class="card">
            <h3>
                Revisi ke-{{ $order->activeRevision->approved_revision_number }}
                dari {{ $order->revision_limit }}
            </h3>
            <p><strong>Catatan klien:</strong> {{ $order->activeRevision->notes }}</p>
            @if($order->activeRevision->admin_notes)
                <p><strong>Catatan admin:</strong> {{ $order->activeRevision->admin_notes }}</p>
            @endif
            <p>
                Diteruskan pada
                {{ $order->activeRevision->forwarded_at?->format('d M Y H:i') ?? '-' }} WIB
            </p>
        </div>
    @endif

    @if(in_array($order->status, [\App\Models\Order::STATUS_PROCESS, \App\Models\Order::STATUS_REVISION], true))
        <div class="card">
            <h3>
                {{ $order->status === \App\Models\Order::STATUS_REVISION
                    ? 'Upload Hasil Revisi'
                    : 'Upload Hasil Awal' }}
            </h3>

            <form
                method="POST"
                action="{{ route('freelancer.tasks.submit', $order) }}"
                enctype="multipart/form-data"
            >
                @csrf
                <label>File hasil</label>
                <input
                    type="file"
                    name="result_file"
                    accept=".jpg,.jpeg,.png,.pdf,.zip,.mp4,.mov,.doc,.docx"
                    required
                >
                <small>Maksimal 50 MB.</small>

                <label>Catatan untuk klien</label>
                <textarea
                    name="notes"
                    maxlength="5000"
                    placeholder="Jelaskan perubahan atau isi hasil yang dikirim."
                >{{ old('notes') }}</textarea>

                <button>Kirim untuk Review Klien</button>
            </form>
        </div>
    @endif

    @if($order->submissions->isNotEmpty())
        <div class="card">
            <h3>Riwayat Hasil yang Dikirim</h3>

            @foreach($order->submissions as $submission)
                <details @if($loop->first) open @endif>
                    <summary>
                        Versi {{ $submission->version }}
                        — {{ ucfirst($submission->submission_type) }}
                        — {{ $submission->submitted_at->format('d M Y H:i') }} WIB
                    </summary>

                    <p>{{ $submission->notes ?: 'Tidak ada catatan.' }}</p>

                    @foreach($submission->files as $file)
                        <p>
                            <a href="{{ route('freelancer.tasks.results.download', [$order, $file]) }}">
                                Download {{ $file->original_name }}
                            </a>
                        </p>
                    @endforeach
                </details>
            @endforeach
        </div>
    @endif

    @if($order->freelancerRevisions->isNotEmpty())
        <div class="card">
            <h3>Riwayat Catatan Revisi</h3>

            @foreach($order->freelancerRevisions as $revision)
                <details @if($loop->first) open @endif>
                    <summary>
                        Pengajuan #{{ $revision->revision_number }}
                        @if($revision->approved_revision_number)
                            — revisi ke-{{ $revision->approved_revision_number }}
                        @endif
                        — {{ $revision->status_label }}
                    </summary>
                    <p><strong>Catatan klien:</strong> {{ $revision->notes }}</p>
                    @if($revision->admin_notes)
                        <p><strong>Catatan admin:</strong> {{ $revision->admin_notes }}</p>
                    @endif
                    @if($revision->resultSubmission)
                        <p>Sudah dijawab melalui hasil versi {{ $revision->resultSubmission->version }}.</p>
                    @endif
                </details>
            @endforeach
        </div>
    @endif
@endsection
