@extends('layouts.app')
@section('title','Daftar Klien')
@section('content')
<div class="card" style="max-width:650px;margin:auto">
<h1>Daftar Klien / UMKM</h1>
<form method="POST" action="{{ route('register.client.process') }}">@csrf
<label>Username</label><input name="username" value="{{ old('username') }}" required>
<label>Nama</label><input name="name" value="{{ old('name') }}" required>
<label>Email</label><input type="email" name="email" value="{{ old('email') }}" required>
<label>Nomor Telepon</label><input name="phone" value="{{ old('phone') }}">
<label>Password</label><input type="password" name="password" required>
<label>Konfirmasi Password</label><input type="password" name="password_confirmation" required>
<button>Daftar</button>
</form>
</div>
@endsection
