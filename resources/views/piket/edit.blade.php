<x-app-layout>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Catatan Piket</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('piket.update', $attendance->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nama / Objek -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama / Keterangan Objek</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $attendance->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jam Pelajaran (Mulai & Selesai) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="lesson_hour_start" class="form-label">Jam Pelajaran Mulai</label>
                                <select name="lesson_hour_start" id="lesson_hour_start" class="form-select @error('lesson_hour_start') is-invalid @enderror">
                                    <option value="">-- Pilih Jam Mulai --</option>
                                    @for ($i = 1; $i <= 11; $i++)
                                        <option value="{{ $i }}" {{ old('lesson_hour_start', $attendance->lesson_hour_start) == $i ? 'selected' : '' }}>
                                            Jam ke-{{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('lesson_hour_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="lesson_hour_end" class="form-label">Jam Pelajaran Selesai</label>
                                <select name="lesson_hour_end" id="lesson_hour_end" class="form-select @error('lesson_hour_end') is-invalid @enderror">
                                    <option value="">-- Pilih Jam Selesai --</option>
                                    @for ($i = 1; $i <= 11; $i++)
                                        <option value="{{ $i }}" {{ old('lesson_hour_end', $attendance->lesson_hour_end) == $i ? 'selected' : '' }}>
                                            Jam ke-{{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('lesson_hour_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="terlambat" {{ old('status', $attendance->status) == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                <option value="izin" {{ old('status', $attendance->status) == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="sakit" {{ old('status', $attendance->status) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="alpa" {{ old('status', $attendance->status) == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                <option value="catatan" {{ old('status', $attendance->status) == 'catatan' ? 'selected' : '' }}>Catatan</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Catatan / Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan / Keterangan</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>