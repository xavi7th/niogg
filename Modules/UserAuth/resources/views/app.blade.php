<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>UserAuth Module - {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="author" content="{{ $author ?? '' }}">
    <meta name="robots" content="index, follow" />

    <link rel="canonical" href="{{ $canonical ?? route('app.index') }}" />

    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $ogUrl ?? rescue(fn() => route('app.index')) }}" />
    <meta property="og:title" content="{{ $title ?? ''}} | {{config('app.name') }}" />
    <meta property="og:description" content="{{$metaDesc ?? 'Enski is an ed-tech company that nurtures young talents for in-demand skillsets with the most qualitative learning resources from top experts and industry leaders setting them up to truly stand out in the global labour market and gain true financial freedom.'}}" />
    <meta property="og:image" content="{{ asset($ogImg ?? '/build/vendor/img/logo-dark.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/js/app.js', 'Modules/UserAuth/resources/js/app.js'])
    <script src="/build/assets/userauth-vendor.js"></script>

    @routes(['auth', 'public'])
    @inertiaHead
  </head>

  <body>
    @inertia
  </body>
