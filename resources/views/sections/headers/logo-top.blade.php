{{-- resources/views/sections/headers/logo-top.blade.php --}}
<header class="site-header header border-b border-white">
  <div class="max-w-7xl mx-auto px-4 py-4 space-y-4">

    {{-- Row 1: logo full width, centred --}}
    <div class="flex justify-center md:justify-center">
      @include('partials.site-logo', [
        'logo_classes'     => 'max-h-16 w-auto object-contain',
        'fallback_classes' => 'text-2xl font-semibold text-center',
      ])
    </div>

    {{-- Row 2: SmartMenus nav --}}
    <nav id="navbar1" class="sm-navbar">
      <span class="sm-toggler-state" id="sm-toggler-state-1"></span>

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

      <div class="sm-collapse">
        @if (has_nav_menu('primary_navigation'))
          @php(
            wp_nav_menu([
              'theme_location' => 'primary_navigation',
              'container'      => false,
              'menu_class'     => 'sm-nav sm-nav--center',
              'walker'         => new \App\Walkers\SmartMenu_Walker(),
              'depth'          => 4,
              'fallback_cb'    => false,
            ])
          )
        @endif
      </div>
    </nav>
  </div>
</header>
