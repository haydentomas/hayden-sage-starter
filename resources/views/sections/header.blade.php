@php
  $layout = get_theme_mod('hayden_header_layout', 'default');
@endphp

@if ($layout === 'logo-top')
  @include('sections.headers.logo-top')
@elseif ($layout === 'nav-center-cta')
  @include('sections.headers.nav-center-cta')
@else
  @include('sections.headers.default')
@endif
