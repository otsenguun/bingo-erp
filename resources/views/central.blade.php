@php
    $config = [
        'appName' => config('app.name'),
        'locale' => ($locale = app()->getLocale()),
        'locales' => config('app.locales'),
        'githubAuth' => config('services.github.client_id'),
        'isDemoMode' => config('app.is_demo_mode'),
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" href='{{ global_asset('images/' . config('config.favicon')) }}'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    {!! settings()->get('custom_html') !!}

    <style>
        /* Hides the Google Translate dropdown */
        .goog-te-gadget {
            display: none !important;
        }
    </style>
</head>

<body class="hold-transition layout-footer-fixed">
    <div id="app"></div>

    {{-- Global configuration object --}}
    <script>
        window.config = @json($config);
    </script>

    {{-- Load the application scripts --}}
    <script src="{{ mix('/js/central.js') }}"></script>

    <script  type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/lewis-kori/vue-google-translate@main/src/utils/translatorRegex.js"></script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
        new google.translate.TranslateElement(
            { 
                pageLanguage: "en", 
                autoDisplay: true 
            },
            'app'
        );
        }
    </script>

</body>

</html>