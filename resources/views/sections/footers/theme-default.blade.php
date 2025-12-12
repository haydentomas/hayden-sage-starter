<footer class="site-footer content-info border-t border-white/10">
 <div class="mx-auto px-4 py-8" style="max-width: var(--site-max-width);">


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

    <div class="grid gap-8 {{ $grid_class }}">
      @for ($i = 1; $i <= $footer_columns; $i++)
        @if (is_active_sidebar("sidebar-footer-{$i}"))
          <div class="footer-column text-sm text-slate-200/80">
            @php(dynamic_sidebar("sidebar-footer-{$i}"))
          </div>
        @endif
      @endfor
    </div>

    {{-- Optional bottom bar --}}
    <div class="mt-8 pt-4 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-400">
      <div>
        &copy; {{ date('Y') }} {{ get_bloginfo('name') }}
      </div>
      <div class="flex flex-wrap gap-4">
        {{-- You can add a small menu or text here later --}}
      </div>
    </div>
  </div>
</footer>
