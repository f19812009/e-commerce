@php
    $siteSettings = \App\Models\DashboardSetting::current();
    $siteTitle = $siteSettings['brand_name'] ?? config('app.name', 'Al-Aqsa Mart');
    $faviconUrl = \App\Models\DashboardSetting::faviconUrl();
@endphp
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title inertia>{{ $siteTitle }}</title>
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); }
        .gradient-text { background: linear-gradient(135deg, #065f46 0%, #047857 50%, #b45309 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-gradient { background: linear-gradient(135deg, #064e3b 0%, #047857 60%, #b45309 100%); }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="bg-[#f8fafc] text-gray-800 antialiased min-h-screen flex flex-col">
    @inertia
</body>
</html>
