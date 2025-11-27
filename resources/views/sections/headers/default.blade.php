{{-- resources/views/sections/headers/default.blade.php --}}

@php
    // Pull logo height from Customizer (default 80px)
    $logo_height = absint(get_theme_mod('hayden_logo_max_height', 80));
    $inline_logo_style = $logo_height ? "max-height:{$logo_height}px; height:auto; width:auto;" : '';
@endphp

<header class="site-header header">
  <div class="site-container mx-auto px-4 py-4">
    <nav id="navbar1" class="sm-navbar">

      {{-- Brand --}}
      <div class="sm-brand flex items-center">
        @include('partials.site-logo', [
          'logo_classes'     => 'w-auto object-contain',
          'logo_style'       => $inline_logo_style,
          'fallback_classes' => 'text-lg font-semibold',
        ])
      </div>

      {{-- Toggler state --}}
      <span class="sm-toggler-state" id="sm-toggler-state-1"></span>

      {{-- Toggler buttons --}}
      <div class="sm-toggler">
        <a class="sm-toggler-anchor sm-toggler-anchor--show"
           href="#sm-toggler-state-1"
           role="button"
           aria-label="Open main menu">
          <span class="sm-toggler-icon sm-toggler-icon--show"></span>
        </a>
        <a class="sm-toggler-anchor sm-toggler-anchor--hide"
           href="#"
           role="button"
           aria-label="Close main menu">
          <span class="sm-toggler-icon sm-toggler-icon--hide"></span>
        </a>
      </div>

      {{-- SmartMenus nav --}}
      <div class="sm-collapse">
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
    </nav>
  </div>
</header>
