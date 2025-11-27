{{--
  Template Name: Projects
--}}

@extends('layouts.app')

@section('content')
  @php
    // All tech terms for the filter buttons
    $tech_terms = get_terms([
      'taxonomy'   => 'project_tech',
      'hide_empty' => true,
    ]);

    // All projects
    $projects = new \WP_Query([
      'post_type'      => 'project',
      'posts_per_page' => -1,
      'orderby'        => 'date',
      'order'          => 'DESC',
    ]);
  @endphp

  <section class="">
    <div class="max-w-7xl mx-auto px-4 space-y-8">
      <h1 class="text-4xl font-bold">Projects</h1>

      {{-- Filter buttons --}}
      <div class="flex flex-wrap gap-3">
        <button class="px-4 py-2 rounded-full  text-sm filter-btn is-active cursor-pointer"
                type="button"
                data-filter="all">
          All
        </button>

        @foreach ($tech_terms as $term)
          <button class="px-4 py-2 rounded-full bg-gray-100 text-sm filter-btn cursor-pointer"
                  type="button"
                  data-filter="{{ $term->slug }}">
            {{ $term->name }}
          </button>
        @endforeach
      </div>

          {{-- Projects grid --}}
      <div class="grid gap-6 md:grid-cols-3" id="projects-grid">
        @while ($projects->have_posts())
          @php $projects->the_post(); @endphp

          @php
            $terms = get_the_terms(get_the_ID(), 'project_tech') ?: [];
            $term_slugs_array = [];

            foreach ($terms as $t) {
              $term_slugs_array[] = $t->slug;
            }

            $term_slugs = implode(' ', $term_slugs_array);
          @endphp

          <article class="project-card rounded-2xl overflow-hidden shadow-soft"
                   data-tech="{{ $term_slugs }}">
            @if (has_post_thumbnail())
              <div class="aspect-[16/9] overflow-hidden">
                {!! get_the_post_thumbnail(null, 'large', ['class' => 'w-full h-full object-cover']) !!}
              </div>
            @endif

            <div class="p-5 space-y-2">
              <h2 class="text-lg font-semibold">{{ get_the_title() }}</h2>
              <p class="text-sm text-body-muted">{{ get_the_excerpt() }}</p>
              <a href="{{ get_permalink() }}" class="text-sm link-primary">View Project</a>
            </div>
          </article>
        @endwhile

        @php wp_reset_postdata(); @endphp
      </div>

      {{-- Load more button (only if more than 6 projects exist) --}}
      @if ($projects->found_posts > 6)
        <div class="mt-8 text-center">
          <button id="projects-load-more"
                  type="button"
                  class="inline-flex items-center px-5 py-2 rounded-full bg-primary text-white font-sans cursor-pointer text-sm filter-btn">
            Load more projects
          </button>
        </div>
      @endif

    </div>
  </section>
@endsection
