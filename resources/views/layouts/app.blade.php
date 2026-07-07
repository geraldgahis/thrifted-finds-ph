<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Dynamic Title -->
    <title>{{ $title ?? 'Thrifted Finds PH | Premium Online Ukay-Ukay Store' }}</title>

    <!-- Dynamic Meta Description -->
    @if(isset($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @else
        <meta name="description"
            content="Shop hand-picked, premium thrifted clothing in the Philippines. Discover rare vintage pieces, branded streetwear, and affordable ukay-ukay online.">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>
    <livewire:navbar />
    {{ $slot }}

    @livewireScripts
</body>

</html>