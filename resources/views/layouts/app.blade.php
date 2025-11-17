<!doctype html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class(' antialiased'))>
    @php(wp_body_open())
    @php(do_action('get_header'))

    <div id="app" class="min-h-screen flex flex-col">
      <a class="sr-only focus:not-sr-only" href="#main">
        {{ __('Skip to content', 'sage') }}
      </a>

      {{-- Global header (we’ll flesh this out in sections.header next) --}}
      @include('sections.header')

      {{-- Main content area --}}
   <main id="main" class="main flex-1">
  <div class="max-w-7xl mx-auto px-4 py-8">
    @hasSection('sidebar')
      {{-- Layout WITH sidebar: 9/3 split on large screens --}}
      <div class="grid gap-8 lg:grid-cols-12">
        <div class="lg:col-span-9">
          @yield('content')
        </div>

        <aside class="sidebar lg:col-span-3">
          @yield('sidebar')
        </aside>
      </div>
    @else
      {{-- Layout WITHOUT sidebar: full-width content --}}
      @yield('content')
    @endif
  </div>
</main>


      {{-- Global footer --}}
      @include('sections.footer')
    </div>
<script src="{{ get_theme_file_uri('resources/js/smartmenus.browser.min.js') }}" defer></script>
<script src="{{ get_theme_file_uri('resources/js/smartmenus-init.js') }}" defer></script>

    @php(do_action('get_footer'))
    @php(wp_footer())

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
