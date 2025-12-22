{{-- resources/views/single.blade.php --}}
@extends('layouts.app')

@section('content')
  @php
    // Global default (Customizer)
    $globalSidebar = (bool) get_theme_mod('hayden_single_show_sidebar', false);
  @endphp

  @while (have_posts())
    @php
      the_post();

      // Per-post override: 'global' | 'show' | 'hide'
      $modeSidebar = get_post_meta(get_the_ID(), '_hayden_single_sidebar', true);
      $modeSidebar = is_string($modeSidebar) && $modeSidebar !== '' ? $modeSidebar : 'global';

      // Resolve final sidebar state
      if ($modeSidebar === 'show') {
          $showSidebar = true;
      } elseif ($modeSidebar === 'hide') {
          $showSidebar = false;
      } else {
          $showSidebar = $globalSidebar;
      }
    @endphp

    <div class="{{ $showSidebar ? 'lg:grid lg:grid-cols-3 lg:gap-10' : '' }}">
      {{-- Main article column --}}
      <div class="{{ $showSidebar ? 'lg:col-span-2 space-y-10' : 'w-full space-y-10' }}">
        @includeFirst(['partials.content-single-' . get_post_type(), 'partials.content-single'])
      </div>

      {{-- Sidebar column --}}
      @if ($showSidebar)
        <aside class="mt-10 lg:mt-0 space-y-8">
          @include('sections.sidebar')
        </aside>
      @endif
    </div>
  @endwhile
@endsection
