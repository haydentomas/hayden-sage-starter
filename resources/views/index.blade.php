{{-- resources/views/index.blade.php --}}
@extends('layouts.app')

@section('content')
  <header class="mb-10">
    <h1 class="text-4xl font-semibold tracking-tight">
      {!! $title ?? __('Latest Posts', 'hayden') !!}
    </h1>
  </header>

  @if (!have_posts())
    <div class="text-lg opacity-70">
      {!! __('Sorry, no results were found.', 'hayden') !!}
    </div>
  @endif

  <div class="space-y-10">
    @while (have_posts()) @php(the_post())
      @include('partials.entry-card')
    @endwhile
  </div>

  <nav class="mt-12">
    {!! get_the_posts_navigation([
      'prev_text' => __('← Older posts', 'hayden'),
      'next_text' => __('Newer posts →', 'hayden'),
    ]) !!}
  </nav>
@endsection
