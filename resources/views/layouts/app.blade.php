<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href={{ asset('assets/output.css') }} rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    {{-- inject css tambahan jika ada page baru --}}
    @stack('styles');
</head>
<body>

    {{-- include berfungsi untuk menambah konten html terpisah dalam layouts seperti navbar agar tetep otomatis di pake oleh child component manapun --}}
    @include('components.navbar');

    {{-- inject all content html redirect this --}}
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src={{ asset('assets/js/index.js') }}></script>

    {{-- inject js tambahan jika ada page baru --}}
    @stack('scripts')
</body>
</html>
