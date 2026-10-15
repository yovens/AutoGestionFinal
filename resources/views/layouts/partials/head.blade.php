<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ trim($__env->yieldContent('title')) ? trim($__env->yieldContent('title')) . ' | ' : '' }}AutoGestion</title>
<meta name="description" content="@yield('description', 'Location et vente de véhicules à ' . $agency['city'] . '.')">
<meta name="theme-color" content="#14213D">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<script>
    // Aplike tèm nan anvan premye desen an pou evite flash.
    try { var t = localStorage.getItem('ag-theme'); if (t === 'light' || t === 'dark') document.documentElement.setAttribute('data-theme', t); } catch (e) {}
</script>
<link rel="preload" href="{{ asset('fonts/archivo-var.woff') }}" as="font" type="font/woff" crossorigin>
<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
@stack('head')
