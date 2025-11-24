{{--
  Template Name: Blog Grid
--}}

@extends('layouts.app')

@section('content')
  @php
    // All blog categories for the filter buttons
    $blog_categories = get_terms([
      'taxonomy'   => 'category',
      'hide_empty' => true,
    ]);

    // All blog posts
    $blog_posts = new \WP_Query([
      'post_type'      => 'post',
      'posts_per_page' => -1,
      'orderby'        => 'date',
      'order'          => 'DESC',
    ]);
  @endphp

  <section>
    <div class="max-w-7xl mx-auto px-4 space-y-8">
      <h1 class="text-4xl font-bold">Blog</h1>

      {{-- Filter buttons --}}
      <div class="flex flex-wrap gap-3">
        <button class="px-4 py-2 rounded-full border text-sm filter-btn is-active cursor-pointer"
                type="button"
                data-filter="all">
          All
        </button>

        @foreach ($blog_categories as $cat)
          <button class="px-4 py-2 rounded-full border text-sm filter-btn cursor-pointer"
                  type="button"
                  data-filter="{{ $cat->slug }}">
            {{ $cat->name }}
          </button>
        @endforeach
      </div>

      {{-- Blog grid --}}
      <div class="grid gap-6 md:grid-cols-3" id="blog-grid">
        @while ($blog_posts->have_posts())
          @php $blog_posts->the_post(); @endphp

          @php
            $terms = get_the_terms(get_the_ID(), 'category') ?: [];
            $term_slugs_array = [];

            foreach ($terms as $t) {
              $term_slugs_array[] = $t->slug;
            }

            $term_slugs = implode(' ', $term_slugs_array);
          @endphp

          <article class="blog-card bg-surface-soft rounded-2xl overflow-hidden shadow-soft"
                   data-category="{{ $term_slugs }}">
            @if (has_post_thumbnail())
              <div class="aspect-[16/9] overflow-hidden">
                {!! get_the_post_thumbnail(null, 'large', ['class' => 'w-full h-full object-cover']) !!}
              </div>
            @endif

            <div class="p-5 space-y-2">
              <h2 class="text-lg font-semibold">{{ get_the_title() }}</h2>
              <p class="text-sm text-body-muted">{{ get_the_excerpt() }}</p>
              <a href="{{ get_permalink() }}" class="text-sm link-primary">Read article</a>
            </div>
          </article>
        @endwhile

        @php wp_reset_postdata(); @endphp
      </div>

      {{-- Load more button (only if more than 6 posts exist) --}}
      @if ($blog_posts->found_posts > 6)
        <div class="mt-8 text-center">
          <button id="blog-load-more"
                  type="button"
                  class="inline-flex items-center px-5 py-2 rounded-full border text-sm filter-btn">
            Load more articles
          </button>
        </div>
      @endif

    </div>
  </section>
@endsection
