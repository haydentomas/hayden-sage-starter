<footer class="site-footer content-info border-t border-white/10 bg-slate-950">
  <div class="mx-auto px-4 py-12" style="max-width: var(--site-max-width);">

    {{-- Top strip (brand + optional area) --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-10 border-b border-white/10">
      <div class="flex items-center gap-3">
        {{-- Optional logo (uses WP Custom Logo if set) --}}
        <div class="shrink-0">
          {!! get_custom_logo() !!}
        </div>

        <div class="leading-tight">
          <div class="text-base font-semibold text-white">
            {{ get_bloginfo('name') }}
          </div>
          <div class="text-sm text-slate-300/80">
            {{ get_bloginfo('description') }}
          </div>
        </div>
      </div>

      {{-- Optional right-side slot (CTA / socials later) --}}
      <div class="text-sm text-slate-300/80">
        {{-- Keep empty for now or add a shortcode / menu later --}}
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

    {{-- Widget grid --}}
    <div class="grid gap-10 {{ $grid_class }} pt-10">
      @for ($i = 1; $i <= $footer_columns; $i++)
        <div class="footer-column text-sm text-slate-200/80">
          @if (is_active_sidebar("sidebar-footer-{$i}"))
            @php(dynamic_sidebar("sidebar-footer-{$i}"))
          @else
            {{-- Empty column placeholder (keeps layout consistent while building) --}}
            <div class="opacity-60">
              <div class="text-white font-semibold text-xs uppercase tracking-wider mb-3">
                {{ __('Footer Column', 'hayden') }} {{ $i }}
              </div>
              <p class="text-slate-300/70">
                {{ __('Add widgets in Customizer → Theme Settings → Footer Column Widgets.', 'hayden') }}
              </p>
            </div>
          @endif
        </div>
      @endfor
    </div>

    {{-- Bottom bar --}}
    <div class="mt-10 pt-6 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-400">
      <div>
        &copy; {{ date('Y') }} {{ get_bloginfo('name') }}. {{ __('All rights reserved.', 'hayden') }}
      </div>

      <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
        {{-- Optional: hook up a "footer" menu later --}}
        {{-- wp_nav_menu(['theme_location' => 'footer_navigation', 'menu_class' => 'flex gap-4']) --}}
      </div>
    </div>

  </div>
</footer>
