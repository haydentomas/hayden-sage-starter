@php
  // theme-default | footer-a | footer-b | footer-c | none
  $variant = get_theme_mod('hayden_footer_variant', 'theme-default');
@endphp

@if ($variant === 'none')
  @php
    // 1) Get the footer page ID (Customizer setting first, then activation fallback)
    $page_id = absint(get_theme_mod(
      'hayden_footer_content_page_id',
      absint(get_option('hayden_footer_page_id', 0))
    ));

    // 2) Get raw block content from that page
    $raw = $page_id ? (string) get_post_field('post_content', $page_id) : '';

    // 3) Render blocks (do_blocks returns HTML)
    $html = $raw ? do_blocks($raw) : '';
  @endphp

  @if (!empty(trim($html)))
    <footer id="site-footer">
     <div class="mx-auto px-4 py-12" style="max-width: var(--site-max-width);">
      {!! $html !!}
      </div>
    </footer>
  @else
    {{-- Show a hint only inside the Customizer preview --}}
    @if (is_customize_preview())
      <footer id="site-footer">
        <div style="padding:16px;border:1px dashed rgba(148,163,184,.5);border-radius:12px;">
          Footer is set to <strong>None (Footer Page)</strong> but no footer content was found.
          <br>
          Page ID: <strong>{{ $page_id ?: 'not set' }}</strong>
        </div>
      </footer>
    @endif
  @endif

@elseif ($variant === 'theme-default')
  @include('sections.footers.theme-default')

@else
  @includeFirst([
    "sections.footers.$variant",
    'sections.footers.footer-a',
  ])
@endif
