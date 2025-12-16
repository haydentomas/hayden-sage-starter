{{-- resources/views/partials/header.blade.php --}}

@php
  // Header layout
  $layout = get_theme_mod('hayden_header_layout', 'default');

  // Header behaviour toggles
  $is_sticky    = (bool) get_theme_mod('hayden_header_sticky', 0);
  $is_fullwidth = (bool) get_theme_mod('hayden_header_full_width', 0);

  // Header wrapper classes
  $header_classes = 'site-header';
  if ($is_sticky) {
    $header_classes .= ' is-sticky';
  }
  if ($is_fullwidth) {
    $header_classes .= ' is-fullwidth';
  }

  // Container classes
  $container_classes = $is_fullwidth
    ? 'w-full px-4'
    : 'mx-auto px-4';

  // Max width only applies when NOT full width
  $container_style = $is_fullwidth
    ? ''
    : 'max-width: var(--site-max-width);';

  // Header content page (used when layout = none)
  $header_page_id = (int) get_theme_mod(
    'hayden_header_content_page_id',
    (int) get_option('hayden_header_page_id', 0)
  );

  $html = '';

  if ($layout === 'none' && $header_page_id) {
    $post = get_post($header_page_id);

    if ($post instanceof \WP_Post && $post->post_status === 'publish') {
      $html = apply_filters('the_content', $post->post_content);
    }
  }
@endphp

{{-- ========================================================= --}}
{{-- HEADER OUTPUT --}}
{{-- ========================================================= --}}

@if ($layout === 'none')
  @if (!empty(trim($html)))
    <header id="site-header" class="{{ $header_classes }}">
      <div class="{{ $container_classes }} py-4" style="{{ $container_style }}">
        {!! $html !!}
      </div>
    </header>
  @endif

@else
  <header id="site-header" class="{{ $header_classes }}">
    <div class="{{ $container_classes }} py-4" style="{{ $container_style }}">
      @if ($layout === 'logo-top')
        @include('sections.headers.logo-top')

      @elseif ($layout === 'nav-center-cta')
        @include('sections.headers.nav-center-cta')

      @else
        @include('sections.headers.default')
      @endif
    </div>
  </header>
@endif
