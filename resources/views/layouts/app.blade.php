<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .plus-jakarta-sans-reguler {
            font-family: "Plus Jakarta Sans", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        .plus-jakarta-sans-bold {
            font-family: "Plus Jakarta Sans", sans-serif;
            font-optical-sizing: auto;
            font-weight: 700;
            font-style: normal;
        }

        .plus-jakarta-sans-SuperBold {
            font-family: "Plus Jakarta Sans", sans-serif;
            font-optical-sizing: auto;
            font-weight: 800;
            font-style: normal;
        }

        .crimson-text-regular {
            font-family: "Crimson Text", serif;
            font-weight: 400;
            font-style: normal;
        }

        .crimson-text-semibold {
            font-family: "Crimson Text", serif;
            font-weight: 600;
            font-style: normal;
        }

        .crimson-text-bold {
            font-family: "Crimson Text", serif;
            font-weight: 700;
            font-style: normal;
        }

        .crimson-text-regular-italic {
            font-family: "Crimson Text", serif;
            font-weight: 400;
            font-style: italic;
        }

        .crimson-text-semibold-italic {
            font-family: "Crimson Text", serif;
            font-weight: 600;
            font-style: italic;
        }

        .crimson-text-bold-italic {
            font-family: "Crimson Text", serif;
            font-weight: 700;
            font-style: italic;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    @yield('content')

    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
