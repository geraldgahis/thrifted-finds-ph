<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard - Thrifted Finds' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#f5f5f7] text-[#1d1d1f] antialiased font-sans flex h-screen overflow-hidden">

    <!-- Inject the isolated Sidebar component -->
    <livewire:sidebar />

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto overflow-x-hidden bg-[#f5f5f7]">
        {{ $slot }}
    </main>

</body>

</html>
