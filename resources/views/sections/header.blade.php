<header class="site-header header border-b border-white">
  <div class="max-w-7xl mx-auto px-4 py-4">
    <nav id="navbar1" class="sm-navbar">
      <div class="flex items-center space-x-2">
        @if (has_custom_logo())
          <div class="h-auto w-36">
            {!! get_custom_logo() !!}
          </div>
        @else
          <a href="{{ home_url('/') }}" class="text-lg font-semibold">
            {{ get_bloginfo('name') }}
          </a>
        @endif
      </div>

      <span class="sm-toggler-state" id="sm-toggler-state-1"></span>

      <div class="sm-toggler">
        <a
          class="sm-toggler-anchor sm-toggler-anchor--show"
          href="#sm-toggler-state-1"
          role="button"
          aria-label="Open main menu"
        >
          <span class="sm-toggler-icon sm-toggler-icon--show"></span>
        </a>
        <a
          class="sm-toggler-anchor sm-toggler-anchor--hide"
          href="#"
          role="button"
          aria-label="Close main menu"
        >
          <span class="sm-toggler-icon sm-toggler-icon--hide"></span>
        </a>
      </div>

      <div class="sm-collapse">
        @if (has_nav_menu('primary_navigation'))
          @php(
            wp_nav_menu([
              'theme_location' => 'primary_navigation', // use Sage's primary menu location
              'container'      => false,
              'menu_class'     => 'sm-nav sm-nav--right',
              'walker' => new \App\Walkers\SmartMenu_Walker(),
              'depth'          => 4,
              'fallback_cb'    => false,
            ])
          )
        @endif
      </div>
    </nav>
  </div>
</header>
