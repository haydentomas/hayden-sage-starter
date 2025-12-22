{{-- resources/views/partials/entry-card.blade.php --}}
@php
  $hasThumb = has_post_thumbnail();
@endphp

<article @php(post_class('group border-b border-black/10 pb-10'))>
  @if ($hasThumb)
    {{-- With thumbnail: 2-column layout --}}
    <div class="grid gap-6 md:grid-cols-12">
      <div class="md:col-span-5">
        <a href="{{ get_permalink() }}"
           class="block aspect-[16/10] overflow-hidden rounded-xl bg-black/5">
          {!! get_the_post_thumbnail(null, 'large', [
            'class' => 'h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]',
            'loading' => 'lazy',
            'decoding' => 'async',
          ]) !!}
        </a>
      </div>

      <div class="md:col-span-7">
        @include('partials.entry-card-meta')
        @include('partials.entry-card-body')
      </div>
    </div>
  @else
    {{-- No thumbnail: full-width (more “Graphy” editorial) --}}
    <div class="max-w-3xl">
      @include('partials.entry-card-meta')
      @include('partials.entry-card-body')
    </div>
  @endif
</article>
