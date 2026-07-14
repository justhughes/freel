<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Contify')</title>
    <style>
        :root { color-scheme: dark; --bg:#07111f; --panel:#101d2f; --line:#24344c; --text:#e5eefb; --muted:#94a3b8; --primary:#22d3ee; --green:#22c55e; --red:#ef4444; --yellow:#f59e0b; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:Arial,sans-serif; background:var(--bg); color:var(--text); }
        a { color:var(--primary); text-decoration:none; }
        nav { position:sticky; top:0; z-index:20; display:flex; gap:14px; align-items:center; padding:14px 24px; background:#081424ee; border-bottom:1px solid var(--line); flex-wrap:wrap; }
        nav .brand { font-size:22px; font-weight:800; color:white; margin-right:auto; }
        nav a { padding:8px 10px; color:#cbd5e1; }
        nav form { margin:0; }
        .container { max-width:1200px; margin:auto; padding:28px 20px 60px; }
        .card { background:var(--panel); border:1px solid var(--line); border-radius:16px; padding:20px; margin-bottom:18px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:16px; }
        .stats { font-size:30px; font-weight:800; margin-top:8px; }
        h1,h2,h3 { margin-top:0; }
        p,small { color:var(--muted); line-height:1.6; }
        label { display:block; margin:10px 0 6px; font-weight:700; }
        input,select,textarea { width:100%; padding:11px 12px; background:#07111f; border:1px solid var(--line); border-radius:9px; color:var(--text); }
        textarea { min-height:100px; resize:vertical; }
        button,.button { display:inline-block; border:0; border-radius:9px; padding:10px 15px; background:#0891b2; color:white; cursor:pointer; font-weight:700; }
        button.danger,.danger { background:#b91c1c; }
        button.success,.success { background:#15803d; }
        button.warning,.warning { background:#b45309; }
        button.secondary,.secondary { background:#475569; }
        table { width:100%; border-collapse:collapse; min-width:760px; }
        th,td { padding:12px; border-bottom:1px solid var(--line); text-align:left; vertical-align:top; }
        th { color:#cbd5e1; }
        .table-wrap { overflow-x:auto; }
        .badge { display:inline-block; padding:5px 9px; border-radius:999px; background:#1e293b; font-size:12px; }
        .row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
        .actions { display:flex; gap:8px; flex-wrap:wrap; }
        .flash { padding:12px 14px; border-radius:10px; margin-bottom:16px; background:#14532d; }
        .errors { padding:12px 14px; border-radius:10px; margin-bottom:16px; background:#7f1d1d; }
        .muted { color:var(--muted); }
        .price { color:var(--primary); font-size:24px; font-weight:800; }
        details { border:1px solid var(--line); border-radius:10px; padding:10px; margin-top:8px; }
        summary { cursor:pointer; font-weight:700; }
        @media(max-width:700px){ nav{padding:12px}.container{padding:20px 12px}.card{padding:15px} }
    </style>
</head>
<body>
<nav>
    <a class="brand" href="{{ route('home') }}">CONTIFY</a>
    @auth
        @if(auth()->user()->isClient())
            <a href="{{ route('client.home') }}">Paket</a>
            <a href="{{ route('client.orders.index') }}">Pesanan Saya</a>
        @elseif(auth()->user()->isFreelancer())
            <a href="{{ route('freelancer.dashboard') }}">Dashboard</a>
            <a href="{{ route('freelancer.jobs.index') }}">Job Board</a>
            <a href="{{ route('freelancer.tasks.index') }}">My Tasks</a>
            <a href="{{ route('freelancer.wallet') }}">Saldo</a>
        @elseif(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.clients.index') }}">Klien</a>
            <a href="{{ route('admin.freelancers.index') }}">Freelancer</a>
            <a href="{{ route('admin.packages.index') }}">Paket</a>
            <a href="{{ route('admin.orders.index') }}">Pesanan</a>
            <a href="{{ route('admin.revisions.index') }}">Revisi</a>
            <a href="{{ route('admin.payments.index') }}">Pembayaran</a>
            <a href="{{ route('admin.withdrawals.index') }}">Withdraw</a>
            <a href="{{ route('admin.vouchers.index') }}">Voucher</a>
        @endif
        <span class="muted">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="secondary">Logout</button></form>
    @endauth
</nav>
<main class="container">
    @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="errors">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="errors"><strong>Periksa kembali:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
</body>
</html>
