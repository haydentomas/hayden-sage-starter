{{-- resources/views/sections/sidebar.blade.php --}}

@php
  // Capture sidebar widgets output
  ob_start();
  dynamic_sidebar('sidebar-primary');
  $sidebar_html = ob_get_clean();

  // Extract each widget (<section>...</section>)
  preg_match_all('/<section\b[^>]*>.*?<\/section>/s', $sidebar_html, $matches);
  $widgets = $matches[0] ?? [];
@endphp

<div class="space-y-6">
  @foreach ($widgets as $widget)

    @php
      // 1) Strip any existing Tailwind rounded classes (rounded, rounded-lg, rounded-2xl, etc)
      $widget = preg_replace('/\brounded(?:-[^\s"]+)?\b/', '', $widget);

      // 2) Inject our global radius class into the first class="..."
      if (strpos($widget, 'class="') !== false) {
        $widget = preg_replace(
          '/class="([^"]*)"/',
          'class="$1 rounded-2xl"',
          $widget,
          1
        );
      } else {
        // If no class attribute exists on <section>, add one
        $widget = preg_replace(
          '/<section\b/',
          '<section class="rounded-2xl"',
          $widget,
          1
        );
      }

      // 3) Tidy double spaces inside class attributes (optional but nice)
      $widget = preg_replace('/class="([^"]*)"/', 'class="' . trim(preg_replace('/\s+/', ' ', '$1')) . '"', $widget, 1);
    @endphp

    {!! $widget !!}

    {{-- Inject TOC immediately after Search widget --}}
    @if (is_singular('post') && strpos($widget, 'widget_search') !== false)
      <section
  id="post-toc-card"
  class="widget sidebar-card js-sidebar-card rounded-2xl bg-surface-soft border  p-5"
>

        <h2 class="widget-title text-sm font-semibold tracking-wide uppercase text-white mb-3">
          {{ __('Table of Contents', 'hayden') }}
        </h2>

        <nav id="post-toc" aria-label="{{ esc_attr__('Table of contents', 'hayden') }}">
          <p class="text-sm text-body-muted">
            {{ __('Loading…', 'hayden') }}
          </p>
        </nav>
      </section>
    @endif

  @endforeach
</div>
