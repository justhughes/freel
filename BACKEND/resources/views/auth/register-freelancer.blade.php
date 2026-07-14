@extends('layouts.app')
@section('title','Daftar Freelancer')
@section('content')
<div class="card" style="max-width:760px;margin:auto">
<h1>Pendaftaran Freelancer</h1>
<p>Pilih bidang yang dikuasai. Admin dapat menyetujui sebagian bidang berdasarkan kuota.</p>
<form method="POST" action="{{ route('register.freelancer.process') }}" enctype="multipart/form-data">@csrf
<div class="grid">
<div><label>Username</label><input name="username" value="{{ old('username') }}" required></div>
<div><label>Nama</label><input name="name" value="{{ old('name') }}" required></div>
<div><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
<div><label>Nomor Telepon</label><input name="phone" value="{{ old('phone') }}" required></div>
<div><label>Password</label><input type="password" name="password" required></div>
<div><label>Konfirmasi Password</label><input type="password" name="password_confirmation" required></div>
<div><label>Pengalaman (tahun)</label><input type="number" name="experience_years" min="0" value="{{ old('experience_years',0) }}" required></div>
<div><label>Link Portofolio</label><input type="url" name="portfolio_url" value="{{ old('portfolio_url') }}"></div>
</div>
<label>Bio</label><textarea name="bio">{{ old('bio') }}</textarea>
<label>File Portofolio (opsional)</label>
<input
    type="file"
    name="portfolio_file"
    accept=".pdf,.jpg,.jpeg,.png,.zip"
>
<small class="muted">
    Format PDF, JPG, JPEG, PNG, atau ZIP. Maksimal 10 MB.
</small>
<label>Bidang Keahlian</label>
<div class="grid">@foreach($categories as $category)<label class="card" style="margin:0"><input style="width:auto" type="checkbox" name="skills[]" value="{{ $category->id }}" @checked(in_array($category->id,old('skills',[])))> {{ $category->name }}</label>@endforeach</div>
<br><button>Kirim Pendaftaran</button>
</form>
</div>
@endsection
