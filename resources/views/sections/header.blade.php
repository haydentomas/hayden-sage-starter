{{-- resources/views/sections/header.blade.php --}}
@php
  $header_layout = get_theme_mod('hayden_header_layout', 'default');
@endphp

@includeIf("sections.headers.{$header_layout}")
