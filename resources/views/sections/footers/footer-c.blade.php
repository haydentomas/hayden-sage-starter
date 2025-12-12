<footer class="site-footer content-info border-t border-white/10">
  @php
    // CTA controls (Customizer)
    $cta_enabled = (bool) get_theme_mod('hayden_footer_c_cta_enabled', true);

    $cta_kicker  = get_theme_mod('hayden_footer_c_cta_kicker', 'Ready when you are');
    $cta_title   = get_theme_mod('hayden_footer_c_cta_title', "Want a WordPress build that's fast, tidy, and easy to manage?");
    $cta_text    = get_theme_mod('hayden_footer_c_cta_text', "Let's turn your design into a lightweight, high-performing site — with a clean editor experience for clients.");

    $btn1_label  = get_theme_mod('hayden_footer_c_cta_btn1_label', 'Start a project');
    $btn1_url    = get_theme_mod('hayden_footer_c_cta_btn1_url', home_url('/contact'));

    $btn2_label  = get_theme_mod('hayden_footer_c_cta_btn2_label', 'View work');
    $btn2_url    = get_theme_mod('hayden_footer_c_cta_btn2_url', home_url('/projects'));

    // Footer widgets columns
    $footer_columns = (int) get_theme_mod('hayden_footer_columns', 4);

    $grid_classes = [
      1 => 'grid-cols-1',
      2 => 'grid-cols-1 md:grid-cols-2',
      3 => 'grid-cols-1 md:grid-cols-3',
      4 => 'grid-cols-1 md:grid-cols-4',
    ];

    $grid_class = $grid_classes[$footer_columns] ?? $grid_classes[4];
  @endphp

  {{-- Top CTA band (optional) --}}
  @if ($cta_enabled)
    <div class="bg-gradient-to-r from-white/5 via-white/0 to-white/5">
      <div class="mx-auto px-4 py-8" style="max-width: var(--site-max-width);">

        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 md:p-10">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl">
              @if (!empty($cta_kicker))
                <p class="text-xs uppercase tracking-widest text-slate-200/70">
                  {{ $cta_kicker }}
                </p>
              @endif

              @if (!empty($cta_title))
                <h2 class="mt-2 text-2xl md:text-3xl font-semibold text-white">
                  {{ $cta_title }}
                </h2>
              @endif

              @if (!empty($cta_text))
                <p class="mt-3 text-sm md:text-base text-slate-200/70">
                  {{ $cta_text }}
                </p>
              @endif
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
              @if (!empty($btn1_label) && !empty($btn1_url))
                <a href="{{ esc_url($btn1_url) }}"
                   class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold
                          bg-[var(--color-primary)] text-black hover:opacity-90 transition">
                  {{ $btn1_label }}
                </a>
              @endif

              @if (!empty($btn2_label) && !empty($btn2_url))
                <a href="{{ esc_url($btn2_url) }}"
                   class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold
                          border border-white/15 text-white hover:bg-white/5 transition">
                  {{ $btn2_label }}
                </a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- Widgets area (NOTE: added top padding so it doesn’t butt into CTA) --}}
  <div class="mx-auto px-4 pb-10 {{ $cta_enabled ? 'pt-8' : 'pt-10' }}" style="max-width: var(--site-max-width);">
    <div class="grid gap-8 {{ $grid_class }}">
      @for ($i = 1; $i <= $footer_columns; $i++)
        @if (is_active_sidebar("sidebar-footer-{$i}"))
          <div class="footer-column text-sm text-slate-200/80">
            @php(dynamic_sidebar("sidebar-footer-{$i}"))
          </div>
        @endif
      @endfor
    </div>

    <div class="mt-10 pt-6 border-t border-white/10 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-xs text-slate-400">
      <div>
        &copy; {{ date('Y') }} {{ get_bloginfo('name') }}. All rights reserved.
      </div>

      <div class="flex flex-wrap gap-4">
        <a class="hover:text-white transition" href="{{ home_url('/privacy-policy') }}">Privacy</a>
        <a class="hover:text-white transition" href="{{ home_url('/terms') }}">Terms</a>
        <a class="hover:text-white transition" href="#top">Back to top</a>
      </div>
    </div>
  </div>
</footer>
