{{-- resources/views/sections/sidebar.blade.php --}}

@php
  // Get all widgets in sidebar-primary
  ob_start();
  dynamic_sidebar('sidebar-primary');
  $sidebar_output = ob_get_clean();

  // Split widgets into an array by widget wrapper
  // Sage uses <section id="..."> for widgets
  preg_match_all('/<section.*?<\/section>/s', $sidebar_output, $matches);
  $widgets = $matches[0] ?? [];
@endphp

{{-- Output widgets one by one, injecting TOC after the Search widget --}}
<div class="space-y-6">
  @foreach ($widgets as $widget)
    {!! $widget !!}

    {{-- Inject TOC immediately after Search widget --}}
    @if (is_singular('post') && str_contains($widget, 'widget_search'))
      <section id="post-toc-card"
               class="widget sidebar-card bg-surface-soft border border-white/5 rounded-2xl p-5">
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
