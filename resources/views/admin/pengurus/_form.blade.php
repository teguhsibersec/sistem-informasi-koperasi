<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="label" for="kode_pengurus">ID Pengurus (3 karakter) *</label>
        <input id="kode_pengurus" name="kode_pengurus" value="{{ old('kode_pengurus', $p?->kode_pengurus) }}" maxlength="3" required class="input font-mono uppercase" placeholder="BDH">
        @error('kode_pengurus') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label" for="nama_pengurus">Nama Pengurus *</label>
        <input id="nama_pengurus" name="nama_pengurus" value="{{ old('nama_pengurus', $p?->nama_pengurus) }}" maxlength="100" required class="input">
        @error('nama_pengurus') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label" for="jabatan_pengurus">Jabatan *</label>
        <select id="jabatan_pengurus" name="jabatan_pengurus" required class="input">
            <option value="">Pilih jabatan</option>
            @foreach (\App\Models\Pengurus::JABATAN as $v => $l) <option value="{{ $v }}" @selected(old('jabatan_pengurus', $p?->jabatan_pengurus) === $v)>{{ $l }}</option> @endforeach
        </select>
        @error('jabatan_pengurus') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div class="sm:col-span-2">
        <label class="label" for="alamat_pengurus">Alamat Pengurus</label>
        <input id="alamat_pengurus" name="alamat_pengurus" value="{{ old('alamat_pengurus', $p?->alamat_pengurus) }}" maxlength="100" class="input">
        @error('alamat_pengurus') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label" for="kota_pengurus">Kota</label>
        <input id="kota_pengurus" name="kota_pengurus" value="{{ old('kota_pengurus', $p?->kota_pengurus) }}" maxlength="40" class="input">
        @error('kota_pengurus') <p class="galat">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label" for="telepon_pengurus">Telepon</label>
        <input id="telepon_pengurus" name="telepon_pengurus" value="{{ old('telepon_pengurus', $p?->telepon_pengurus) }}" maxlength="15" class="input" inputmode="numeric">
        @error('telepon_pengurus') <p class="galat">{{ $message }}</p> @enderror
    </div>
</div>
