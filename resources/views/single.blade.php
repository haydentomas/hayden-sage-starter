{{-- resources/views/single.blade.php --}}
@extends('layouts.app')

@section('content')
  <div class="lg:grid lg:grid-cols-3 lg:gap-10">
    {{-- Main article column --}}
    <div class="lg:col-span-2 space-y-10">
      @while (have_posts()) @php(the_post())
        @includeFirst(['partials.content-single-' . get_post_type(), 'partials.content-single'])
      @endwhile
    </div>

    {{-- Sidebar column --}}
    <aside class="mt-10 lg:mt-0 space-y-8">
      @include('sections.sidebar')
    </aside>
  </div>
@endsection
