<a href="{{ esc_url(home_url('/')) }}"
   class="site-logo inline-flex items-center"
   aria-label="{{ esc_attr(get_bloginfo('name')) }}">

  @if (has_custom_logo())
    {!! wp_get_attachment_image(
      get_theme_mod('custom_logo'),
      'full',
      false,
      [
        'class' => $logo_classes ?? '',
        'style' => $logo_style ?? '',
      ]
    ) !!}
  @else
    <span class="{{ $fallback_classes ?? 'font-semibold' }}">
      {{ get_bloginfo('name') }}
    </span>
  @endif

</a>
