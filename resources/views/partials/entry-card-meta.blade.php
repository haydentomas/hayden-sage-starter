{{-- resources/views/partials/entry-card-meta.blade.php --}}
<div class="mb-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm opacity-70">
  @if (is_sticky())
    <span class="rounded-full border border-black/15 px-2 py-0.5 text-xs opacity-90">
      {{ __('Featured', 'hayden') }}
    </span>
  @endif

  <time datetime="{{ esc_attr(get_post_time('c', true)) }}">
    {{ get_the_date() }}
  </time>

  <span aria-hidden="true">•</span>

  <span>
    {{ __('By', 'hayden') }} {{ get_the_author() }}
  </span>
</div>
