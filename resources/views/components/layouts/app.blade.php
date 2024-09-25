<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>{{ $title ?? 'Hello Laundry' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
    rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>

<body class="bg-slate-200">
  <main>
    <header class="bg-transparent absolute top-0 left-0 w-full flex justify-center items-center z-10">
      @livewire('partials.navbar')
    </header>
    <div class="container mx-auto pt-36">
      {{ $slot }}
      @livewireScripts
    </div>
    <footer>
      @livewire('partials.footer')
    </footer>
  </main>
</body>

</html>
