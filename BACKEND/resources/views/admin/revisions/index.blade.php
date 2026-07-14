@extends('layouts.app')

@section('title', 'Pengajuan Revisi')

@section('content')
    <div class="row">
        <div>
            <h1>Pengajuan Revisi</h1>
            <p>Admin memeriksa catatan klien sebelum diteruskan kepada freelancer.</p>
        </div>
    </div>

    <form method="GET" class="card">
        <label>Filter Status</label>
        <select name="status">
            <option value="">Semua status</option>
            @foreach([
                \App\Models\OrderRevision::STATUS_PENDING_ADMIN,
                \App\Models\OrderRevision::STATUS_FORWARDED,
                \App\Models\OrderRevision::STATUS_IN_PROGRESS,
                \App\Models\OrderRevision::STATUS_COMPLETED,
                \App\Models\OrderRevision::STATUS_REJECTED,
            ] as $revisionStatus)
                <option value="{{ $revisionStatus }}" @selected($status === $revisionStatus)>
                    {{ str_replace('_', ' ', ucfirst($revisionStatus)) }}
                </option>
            @endforeach
        </select>
        <button>Filter</button>
    </form>

    @forelse($revisions as $revision)
        <div class="card">
            <div class="row">
                <div>
                    <h2>
                        <a href="{{ route('admin.orders.show', $revision->order) }}">
                            {{ $revision->order->order_code }}
                        </a>
                    </h2>
                    <p>
                        {{ $revision->order->client->name }}
                        — {{ $revision->order->package->name }}
                        — freelancer {{ $revision->order->freelancer?->name ?? '-' }}
                    </p>
                </div>
                <span class="badge">{{ $revision->status_label }}</span>
            </div>

            <p>
                <strong>Diajukan:</strong>
                {{ $revision->requested_at->format('d M Y H:i') }} WIB
            </p>
            <p>
                <strong>Penggunaan revisi pesanan:</strong>
                {{ $revision->order->revision_used }}/{{ $revision->order->revision_limit }}
            </p>
            <p><strong>Catatan klien:</strong> {{ $revision->notes }}</p>

            @if($revision->submission)
                <p>
                    Catatan ini dibuat untuk hasil versi {{ $revision->submission->version }}.
                </p>
                @foreach($revision->submission->files as $file)
                    <p>
                        <a href="{{ route('admin.orders.results.download', [$revision->order, $file]) }}">
                            Download {{ $file->original_name }}
                        </a>
                    </p>
                @endforeach
            @endif

            @if($revision->admin_notes)
                <p><strong>Catatan admin:</strong> {{ $revision->admin_notes }}</p>
            @endif

            @if($revision->resultSubmission)
                <p>
                    <strong>Hasil revisi:</strong>
                    versi {{ $revision->resultSubmission->version }},
                    dikirim {{ $revision->resultSubmission->submitted_at->format('d M Y H:i') }} WIB
                </p>
                @foreach($revision->resultSubmission->files as $file)
                    <p>
                        <a href="{{ route('admin.orders.results.download', [$revision->order, $file]) }}">
                            Download {{ $file->original_name }}
                        </a>
                    </p>
                @endforeach
            @endif

            @if($revision->status === \App\Models\OrderRevision::STATUS_PENDING_ADMIN)
                <div class="grid">
                    <form method="POST" action="{{ route('admin.revisions.forward', $revision) }}">
                        @csrf
                        <label>Catatan tambahan untuk freelancer (opsional)</label>
                        <textarea name="admin_notes" maxlength="5000"></textarea>
                        <button class="success">Teruskan ke Freelancer</button>
                    </form>

                    <form method="POST" action="{{ route('admin.revisions.reject', $revision) }}">
                        @csrf
                        <label>Alasan penolakan</label>
                        <textarea name="admin_notes" maxlength="5000" required></textarea>
                        <button class="danger">Tolak Pengajuan</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="card">Belum ada pengajuan revisi.</div>
    @endforelse
@endsection
