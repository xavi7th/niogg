<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>AppUser Module - {{ config('app.name', 'Laravel') }}</title>

    <meta name="author" content="{{ $author ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="description" content="{{ $description ?? '' }}">

    <!-- <meta name="robots" content="index, follow" /> -->
    <meta name="format-detection" content="telephone=yes">

    <link rel="canonical" href="{{ $canonical ?? route('app.index') }}" />

    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $ogUrl ?? route('app.index') }}" />
    <meta property="og:title" content="{{ $title ?? ''}} | {{ config('app.name') }}" />
    <meta property="og:description" content="{{ $metaDesc ?? ''}}" />
    <meta property="og:image" content="{{ asset($ogImg ?? '/build/vendor/img/logo-dark.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @routes
    @inertiaHead
  </head>

  <body>
    <p>Module: {!! config('appuser.name') !!} app.blade file</p>

    @inertia

    {{-- Vite JS --}}
    {{-- @vite(\Nwidart\Modules\Module::getAssets()) --}}
    @vite(['resources/js/app.js', 'Modules/AppUser/resources/js/app.js'])
    <script src="/build/assets/vendor/appuser-vendor.js"></script>
  </body>
