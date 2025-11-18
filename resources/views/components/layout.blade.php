{{-- resources/views/components/layout.blade.php --}}
<!doctype html>
<html {!! get_language_attributes() !!}>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class('bg-surface text-body antialiased'))>
    @php(wp_body_open())
    @php(do_action('get_header'))

    <div id="app" class="min-h-screen flex flex-col">
      <a class="sr-only focus:not-sr-only" href="#main">
        {{ __('Skip to content', 'sage') }}
      </a>

      {{-- Primary navigation / header (wraps sections.header) --}}
      <x-nav.primary />

      {{-- Main content slot --}}
      <main id="main" class=" max-w-7xl mx-auto px-4 py-4 pt-10 pb-10">
        {{ $slot }}
      </main>

      {{-- Site footer (wraps sections.footer) --}}
      <x-footer />
    </div>

    <script src="{{ get_theme_file_uri('resources/js/smartmenus.browser.min.js') }}" defer></script>
    <script src="{{ get_theme_file_uri('resources/js/smartmenus-init.js') }}" defer></script>

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
