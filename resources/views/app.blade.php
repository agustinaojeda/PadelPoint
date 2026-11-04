<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#042f2e">

        @php
            $metadatos = app(\App\Http\Support\MetadatosPagina::class)->para(request());
        @endphp

        <title inertia>{{ $metadatos['titulo'] }}</title>

        @if ($metadatos['indexable'])
            <meta name="description" content="{{ $metadatos['descripcion'] }}">
            <link rel="canonical" href="{{ $metadatos['url'] }}">

            <meta property="og:type" content="website">
            <meta property="og:site_name" content="{{ config('app.name', 'PadelPoint') }}">
            <meta property="og:locale" content="es_AR">
            <meta property="og:title" content="{{ $metadatos['titulo'] }}">
            <meta property="og:description" content="{{ $metadatos['descripcion'] }}">
            <meta property="og:url" content="{{ $metadatos['url'] }}">
            <meta property="og:image" content="{{ $metadatos['imagen'] }}">
            <meta property="og:image:type" content="image/jpeg">
            <meta property="og:image:width" content="{{ $metadatos['imagenAncho'] }}">
            <meta property="og:image:height" content="{{ $metadatos['imagenAlto'] }}">
            <meta property="og:image:alt" content="PadelPoint: reservá tu cancha de pádel online">

            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="{{ $metadatos['titulo'] }}">
            <meta name="twitter:description" content="{{ $metadatos['descripcion'] }}">
            <meta name="twitter:image" content="{{ $metadatos['imagen'] }}">
        @else
            <meta name="robots" content="noindex, nofollow">
        @endif

        <link rel="icon" type="image/png" href="/logo.svg">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
