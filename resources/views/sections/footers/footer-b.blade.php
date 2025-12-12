<footer class="site-footer content-info border-t border-white/10 bg-slate-950">
  <div class=" mx-auto px-4 py-10" style="max-width: var(--site-max-width);">

    {{-- Top minimal brand line --}}
    <div class="text-center mb-8">
      <div class="text-sm font-semibold tracking-wide text-slate-100">
        {{ get_bloginfo('name') }}
      </div>
      <div class="mt-1 text-xs text-slate-400">
        {{ get_bloginfo('description') }}
      </div>
    </div>

    @php
      $footer_columns = (int) get_theme_mod('hayden_footer_columns', 3);

      $grid_classes = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-4',
      ];

      $grid_class = $grid_classes[$footer_columns] ?? $grid_classes[3];
    @endphp

    {{-- Widget panel --}}
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 md:p-8">
      <div class="grid gap-8 {{ $grid_class }}">
        @for ($i = 1; $i <= $footer_columns; $i++)
          @if (is_active_sidebar("sidebar-footer-{$i}"))
            <div class="footer-column text-sm text-slate-200/80">
              @php(dynamic_sidebar("sidebar-footer-{$i}"))
            </div>
          @endif
        @endfor
      </div>
    </div>

    {{-- Clean centred bottom bar --}}
    <div class="mt-8 pt-6 border-t border-white/10 text-center text-xs text-slate-400">
      <span>&copy; {{ date('Y') }} {{ get_bloginfo('name') }}.</span>
      <span class="mx-2 opacity-50">•</span>
      <span>All rights reserved.</span>
    </div>

  </div>
</footer>
