@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Rekap Catatan Piket</h3>
        @if(in_array(strtolower(auth()->user()->role), ['admin', 'wakasek', 'guru_piket', 'guru']))
            <a href="{{ route('piket.create') }}" class="btn btn-primary">+ Tambah Catatan</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal & Waktu</th>
                            <th>Nama Subjek</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Petugas Piket (Pencatat)</th>
                            @if(in_array(strtolower(auth()->user()->role), ['admin', 'wakasek']))
                                <th class="text-center">Aksi (Admin)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $index => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->date }} <span class="text-muted small">{{ $item->time }}</span></td>
                                <td class="fw-bold">{{ $item->name }}</td>
                                <td>
                                    <span class="badge {{ $item->type === 'siswa' ? 'bg-info text-dark' : 'bg-secondary' }}">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->status === 'terlambat' ? 'warning' : ($item->status === 'sakit' ? 'info' : ($item->status === 'izin' ? 'primary' : 'danger')) }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>{{ $item->description ?? '-' }}</td>
                                {{-- PERBAIKAN: Menampilkan Nama Petugas Pencatat yang Sesuai --}}
                                <td>
                                    <span class="fw-semibold text-primary">
                                        {{ $item->recorder->name ?? 'Sistem' }}
                                    </span>
                                </td>
                                @if(in_array(strtolower(auth()->user()->role), ['admin', 'wakasek']))
                                    <td class="text-center">
                                        <a href="{{ route('piket.edit', $item->id) }}" class="btn btn-sm btn-outline-warning me-1">Edit</a>
                                        <form action="{{ route('piket.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada catatan piket yang terekam.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection