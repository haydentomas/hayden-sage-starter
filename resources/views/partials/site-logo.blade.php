{{-- resources/views/partials/site-logo.blade.php --}}
@php
  $site_name        = get_bloginfo('name');
  $home_url         = home_url('/');
  $logo_classes     = $logo_classes ?? 'max-h-12 w-auto object-contain';
  $fallback_classes = $fallback_classes ?? 'text-lg font-semibold';
@endphp

@if (has_custom_logo())
  @php($custom_logo_id = get_theme_mod('custom_logo'))

  <a href="{{ $home_url }}"
     class="inline-flex items-center"
     aria-label="{{ $site_name }}">
    {!! wp_get_attachment_image($custom_logo_id, 'full', false, [
      'class' => $logo_classes,
    ]) !!}
  </a>
@else
  <a href="{{ $home_url }}"
     class="inline-flex items-center {{ $fallback_classes }}">
    {{ $site_name }}
  </a>
@endif
