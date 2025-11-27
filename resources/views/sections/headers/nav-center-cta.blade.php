{{-- resources/views/sections/headers/nav-center-cta.blade.php --}}

@php
    // Logo height from Customizer
    $logo_height = absint(get_theme_mod('hayden_logo_max_height', 80));
    $inline_logo_style = $logo_height ? "max-height:{$logo_height}px; height:auto; width:auto;" : '';

    // CTA settings
    $cta_label = get_theme_mod('hayden_header_cta_label', __('Start a Project', 'hayden'));
    $cta_url   = get_theme_mod('hayden_header_cta_url', home_url('/start-a-project'));
@endphp

<header class="site-header header ">
  <div class="site-container mx-auto px-4 py-4">
    <nav id="navbar1" class="sm-navbar flex items-center justify-between gap-6 w-full">

      {{-- Brand / logo on the left --}}
      <div class="sm-brand flex items-center">
        @include('partials.site-logo', [
          'logo_classes'     => 'w-auto object-contain',
          'logo_style'       => $inline_logo_style,
          'fallback_classes' => 'text-lg font-semibold',
        ])
      </div>

      {{-- Toggler state (SmartMenus requirement) --}}
      <span class="sm-toggler-state" id="sm-toggler-state-1"></span>

      {{-- Mobile toggler buttons --}}
      <div class="sm-toggler lg:hidden">
        <a class="sm-toggler-anchor sm-toggler-anchor--show"
           href="#sm-toggler-state-1"
           role="button"
           aria-label="@lang('Open main menu')">
          <span class="sm-toggler-icon sm-toggler-icon--show"></span>
        </a>
        <a class="sm-toggler-anchor sm-toggler-anchor--hide"
           href="#"
           role="button"
           aria-label="@lang('Close main menu')">
          <span class="sm-toggler-icon sm-toggler-icon--hide"></span>
        </a>
      </div>

      {{-- Primary navigation (takes remaining space) --}}
      <div class="sm-collapse flex-1">
        @if (has_nav_menu('primary_navigation'))
          @php
            wp_nav_menu([
              'theme_location' => 'primary_navigation',
              'container'      => false,
              'menu_class'     => 'sm-nav sm-nav--right',
              'walker'         => new \App\Walkers\SmartMenu_Walker(),
              'depth'          => 4,
              'fallback_cb'    => false,
            ]);
          @endphp
        @endif
      </div>

      {{-- CTA on the far right (desktop only), still inside the navbar --}}
      <div class="shrink-0 hidden lg:block">
        <a href="{{ esc_url($cta_url) }}"
           class="header-cta inline-flex uppercase items-center rounded-full px-5 py-2 text-sm font-sans font-semibold
                  no-underline
                  bg-primary text-white hover:bg-primary/90
                  focus-visible:outline-none focus-visible:ring-2
                  focus-visible:ring-primary focus-visible:ring-offset-2
                  focus-visible:ring-offset-surface">
          {{ esc_html($cta_label) }}
        </a>
      </div>

    </nav>
  </div>
</header>
