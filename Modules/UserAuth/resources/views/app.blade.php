<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title> {{ $pageTitle . ' - ' ?? '' }} {{ config('app.alt_name') }}</title>

    <meta name="author" content="{{ $author ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="description" content="{{ $description ?? '' }}">

    <meta name="robots" content="noindex, nofollow" />
    <meta name="format-detection" content="telephone=no">

    <link href="{{ asset('/build/img/favicon.png') }}" rel="icon" type="image/png" sizes="512x512">

    <link rel="canonical" href="{{ $canonical ?? route('app.index') }}" />

    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $ogUrl ?? Request::fullUrl() }}" />
    <meta property="og:title" content="{{ $pageTitle ?? ''}} - {{ config('app.alt_name') }}" />
    <meta property="og:description" content="{{ $metaDesc ?? ''}}" />
    <meta property="og:image" content="{{ asset($ogImg ?? '/build/img/niogg-gold.png') }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    <meta name="twitter:card" content="summary_large_images" />
    <meta name="twitter:site" content="@insightNig40403" />
    <meta name="twitter:url" content="{{ Request::fullUrl() }}" />
    <meta name="twitter:title" content="{{ $pageTitle ?? ''}} - {{ config('app.alt_name') }}" />
    <meta name="twitter:description" content="{{$twitterDescription ?? $metaDesc ?? ''}}" />
    <meta name="twitter:image" content="{{ asset($ogImg ?? '/build/img/niogg-gold.png') }}" />

    <link itemprop="url" href="{{ Request::fullUrl() }}">
    <meta itemprop="name" content="{{ config('app.name') }}">
    <meta name="theme-color" content="#a18802">
    <meta name="apple-mobile-web-app-status-bar-style" content="#a18802">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['Modules/UserAuth/resources/js/app.js'])

    @routes(['auth', 'public'])
    @inertiaHead
  </head>

  <body>
    @inertia
  </body>
</html>
