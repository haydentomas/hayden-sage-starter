<article {!! post_class('h-entry') !!}>

  @php
    // Global default (Customizer)
    $globalFeatured = (bool) get_theme_mod('hayden_single_show_featured', true);

    // Per-post override: global|show|hide
    $modeFeatured = get_post_meta(get_the_ID(), '_hayden_single_featured', true) ?: 'global';

    $showFeatured = match ($modeFeatured) {
      'show'  => true,
      'hide'  => false,
      default => $globalFeatured,
    };
  @endphp

  {{-- Header --}}
  <header class="mb-8 border-b border-white/10 pb-6">
    <h1 class="p-name text-3xl md:text-4xl font-bold mb-3">
      {{ $title }}
    </h1>

    @include('partials.entry-meta')
  </header>

  {{-- Featured image --}}
  @if ($showFeatured && has_post_thumbnail())
    <figure class="mb-10 overflow-hidden rounded-2xl bg-black/5">
      {!! get_the_post_thumbnail(null, 'large', [
        'class'    => 'w-full h-auto',
        'loading'  => 'eager',
        'decoding' => 'async',
      ]) !!}
    </figure>
  @endif

  {{-- Content --}}
  <div class="e-content prose max-w-none">
    {!! apply_filters('the_content', get_the_content()) !!}
  </div>

  {{-- Multi-page post pagination --}}
  @if (!empty($pagination))
    <footer class="mt-8">
      <nav class="page-nav text-sm" aria-label="Post pages">
        {!! $pagination !!}
      </nav>
    </footer>
  @endif

  {{-- Previous / Next navigation --}}
  @php
    $previous_link = get_previous_post_link(
      '%link',
      '<span class="block text-xs uppercase text-body-muted">&larr; Previous Post</span>
       <span class="block font-semibold font-sans mt-1">%title</span>'
    );

    $next_link = get_next_post_link(
      '%link',
      '<span class="block text-xs uppercase text-body-muted">Next Post &rarr;</span>
       <span class="block font-semibold text-right font-sans mt-1">%title</span>'
    );
  @endphp

  @if ($previous_link || $next_link)
    <nav class="mt-10 border-t border-white/10 pt-6 flex justify-between gap-8 text-sm">
      <div class="max-w-[45%]">{!! $previous_link !!}</div>
      <div class="max-w-[45%] text-right ml-auto">{!! $next_link !!}</div>
    </nav>
  @endif

{{-- Comments --}}
<section class="hayden-comments mt-12 border-t border-white/10 pt-10">
  @php(comments_template())
</section>


</article>
