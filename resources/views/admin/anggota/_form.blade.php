<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="label" for="nis_anggota">Identitas Anggota (NIS) *</label>
        <input id="nis_anggota" name="nis_anggota" value="{{ old('nis_anggota', $a?->nis_anggota) }}" maxlength="20" required class="input" inputmode="numeric">
        @error('nis_anggota') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div class="sm:col-span-2">
        <label class="label" for="nama_anggota">Nama Anggota *</label>
        <input id="nama_anggota" name="nama_anggota" value="{{ old('nama_anggota', $a?->nama_anggota) }}" maxlength="100" required class="input">
        @error('nama_anggota') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label" for="kelas_anggota">Kelas *</label>
        <input id="kelas_anggota" name="kelas_anggota" value="{{ old('kelas_anggota', $a?->kelas_anggota) }}" maxlength="15" required class="input" placeholder="VII-A">
        @error('kelas_anggota') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div class="sm:col-span-2">
        <label class="label" for="alamat_anggota">Alamat Anggota</label>
        <input id="alamat_anggota" name="alamat_anggota" value="{{ old('alamat_anggota', $a?->alamat_anggota) }}" maxlength="100" class="input">
        @error('alamat_anggota') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label" for="kota_anggota">Kota</label>
        <input id="kota_anggota" name="kota_anggota" value="{{ old('kota_anggota', $a?->kota_anggota) }}" maxlength="35" class="input">
        @error('kota_anggota') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label" for="no_hp_anggota">Telepon</label>
        <input id="no_hp_anggota" name="no_hp_anggota" value="{{ old('no_hp_anggota', $a?->no_hp_anggota) }}" maxlength="15" class="input" inputmode="numeric">
        @error('no_hp_anggota') <p class="galat">{{ $message }}</p> @enderror
    </div>
    @if ($a)
        <div>
            <label class="label" for="status_anggota">Status *</label>
            <select id="status_anggota" name="status_anggota" class="input">
                <option value="aktif" @selected(old('status_anggota', $a->status_anggota) === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(old('status_anggota', $a->status_anggota) === 'nonaktif')>Nonaktif</option>
            </select>
        </div>
    @endif
</div>
