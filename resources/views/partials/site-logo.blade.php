{{-- resources/views/partials/site-logo.blade.php --}}

@php
  $logo_id = get_theme_mod('custom_logo');
@endphp

@if ($logo_id)
  {!! wp_get_attachment_image($logo_id, 'full', false, [
      'class' => $logo_classes ?? 'w-auto object-contain',
      'style' => $logo_style ?? '',
  ]) !!}
@else
  <div class="{{ $fallback_classes ?? '' }}">
    {{ get_bloginfo('name') }}
  </div>
@endif
