{{-- resources/views/sections/headers/default.blade.php --}}
<header class="site-header header border-b border-white">
  <div class="max-w-7xl mx-auto px-4 py-4">
<!-- Navbar 1 -->
<nav id="navbar1" class="sm-navbar">
  <h1 class="sm-brand"><a href="#">Navbar 1</a></h1>

  <span class="sm-toggler-state" id="sm-toggler-state-1"></span>
  <div class="sm-toggler">
    <a class="sm-toggler-anchor sm-toggler-anchor--show" href="#sm-toggler-state-1" role="button" aria-label="Open main menu">
      <span class="sm-toggler-icon sm-toggler-icon--show"></span>
    </a>
    <a class="sm-toggler-anchor sm-toggler-anchor--hide" href="#" role="button" aria-label="Close main menu">
      <span class="sm-toggler-icon sm-toggler-icon--hide"></span>
    </a>
  </div>

  <div class="sm-collapse">
    <ul class="sm-nav sm-nav--right">
      <li class="sm-nav-item"><a class="sm-nav-link" href="#">Link</a></li>
      <li class="sm-nav-item"><a class="sm-nav-link" href="#">Link</a></li>
      <li class="sm-nav-item">
        <a class="sm-nav-link sm-sub-toggler" href="#">Sub</a>
        <ul class="sm-sub">
          <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
          <li class="sm-sub-item">
            <a class="sm-sub-link sm-sub-toggler" href="#">Sub</a>
            <ul class="sm-sub">
              <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
              <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
              <li class="sm-sub-item">
                <a class="sm-sub-link sm-sub-toggler" href="#">Sub</a>
                <ul class="sm-sub">
                  <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
                  <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
                  <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
                </ul>
              </li>
              <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
            </ul>
          </li>
          <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
          <li class="sm-sub-item-separator"></li>
          <li class="sm-sub-item"><a class="sm-sub-link sm-disabled" href="#">Disabled</a></li>
        </ul>
      </li>
      <li class="sm-nav-item"><a class="sm-nav-link" href="#">Link</a></li>
      <li class="sm-nav-item"><a class="sm-nav-link" href="#">Link</a></li>
      <li class="sm-nav-item">
        <a class="sm-nav-link sm-nav-link--split" href="#">Link</a
        ><button class="sm-nav-link sm-nav-link--split sm-sub-toggler" aria-label="Toggle sub menu"></button>
        <ul class="sm-sub">
          <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
          <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
          <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
          <li class="sm-sub-item">
            <a class="sm-sub-link sm-sub-link--split" href="#">Link</a
            ><button class="sm-sub-link sm-sub-link--split sm-sub-toggler" aria-label="Toggle sub menu"></button>
            <ul class="sm-sub">
              <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
              <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
              <li class="sm-sub-item"><a class="sm-sub-link" href="#">Link</a></li>
              <li class="sm-sub-item">
                <a class="sm-sub-link sm-sub-link--split" href="https://www.yahoo.com">Link</a
                ><button class="sm-sub-link sm-sub-link--split sm-sub-toggler" aria-label="Toggle sub menu"></button>
                <ul class="sm-sub">
                  <li class="sm-sub-item"><a class="sm-sub-link" href="https://www.google.com">Link</a></li>
                  <li class="sm-sub-item"><a class="sm-sub-link" href="https://x.com">Link</a></li>
                  <li class="sm-sub-item"><a class="sm-sub-link" href="https://haydentomas.co.uk">Link</a></li>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
      </li>
      <li class="sm-nav-item"><a class="sm-nav-link" href="#">Link</a></li>
      <li class="sm-nav-item sm-nav-item--has-mega">
        <a class="sm-nav-link sm-sub-toggler" href="#">Mega</a>
        <div class="sm-sub sm-sub--mega">
          <div style="border: 1px dashed rgba(0, 0, 0, 0.2); padding: 1rem">
           <div class="col">
            1
           </div>
           <div class="col">2</div>
           <div class="col">3</div>
            <div class="col">4</div>
          </div>
        </div>
      </li>


    </ul>
  </div>
</nav>

  </div>
</header>
