{{-- resources/views/partials/comments.blade.php --}}
@if (! post_password_required())
  <section id="comments" class="mt-12 border-t border-white/10 pt-10 space-y-10">

    {{-- Existing comments --}}
    @if ($responses())
      <div>
        <h2 class="text-xl font-semibold mb-6">
          {!! $title !!}
        </h2>

        <ol class="space-y-6">
          {!! $responses !!}
        </ol>

        @if ($paginated())
          <nav aria-label="{{ esc_attr__('Comment navigation', 'hayden') }}"
               class="mt-6 flex justify-between text-sm text-body-muted">
            @if ($previous())
              <div class="previous">
                {!! $previous !!}
              </div>
            @endif

            @if ($next())
              <div class="next ml-auto">
                {!! $next !!}
              </div>
            @endif
          </nav>
        @endif
      </div>
    @endif

    {{-- Closed notice --}}
    @if ($closed())
      <x-alert type="warning">
        {!! __('Comments are closed.', 'hayden') !!}
      </x-alert>
    @endif

    {{-- Comment form --}}
    <div class="bg-surface-soft/60 border border-white/5 rounded-2xl p-6">
      <h3 class="text-lg font-semibold mb-4">
        {{ __('Leave a comment', 'hayden') }}
      </h3>

      @php(comment_form())
    </div>
  </section>
@endif
