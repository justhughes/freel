@extends('layouts.app')
@section('title','Login Contify')
@section('content')
<div class="card" style="max-width:520px;margin:auto">
    <h1>Login Contify</h1>
    <p>Masuk sebagai klien, freelancer, atau admin.</p>
    <form method="POST" action="{{ route('login.process') }}">
        @csrf
        <label>Username atau Email</label>
        <input name="login" value="{{ old('login') }}" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <label><input type="checkbox" name="remember" value="1" style="width:auto"> Ingat saya</label>
        <button type="submit">Login</button>
    </form>
    <hr style="border-color:#24344c;margin:20px 0">
    <div class="actions">
        <a class="button secondary" href="{{ route('register.client') }}">Daftar Klien</a>
        <a class="button secondary" href="{{ route('register.freelancer') }}">Daftar Freelancer</a>
    </div>
</div>
@endsection
