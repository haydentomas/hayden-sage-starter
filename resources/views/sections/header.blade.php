{{-- resources/views/sections/header.blade.php --}}
@php
  // Default layout slug if nothing set
  $header_layout = $header_layout ?? 'default';
@endphp

@includeIf("sections.headers.{$header_layout}")
