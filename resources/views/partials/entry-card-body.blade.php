{{-- resources/views/partials/entry-card-body.blade.php --}}
<h2 class="">
  <a href="{{ get_permalink() }}" class="hover:underline underline-offset-4">
    {{ get_the_title() }}
  </a>
</h2>

<div class="">
  <p>{{ has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_shortcodes(get_the_content('')), 34) }}</p>
</div>

<div class="mt-4">
  <a href="{{ get_permalink() }}" class="inline-flex items-center gap-2 text-sm font-medium hover:underline underline-offset-4">
    {{ __('Continue reading', 'hayden') }} <span aria-hidden="true">→</span>
  </a>
</div>
