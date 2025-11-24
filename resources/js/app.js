import.meta.glob([
  '../images/**',
  '../fonts/**',
]);











document.addEventListener('DOMContentLoaded', () => {

  // Get page sizes from localized PHP values or fall back to 6
  function getPageSize(type) {
    var fallback = 6;

    if (typeof window.SageGridSettings === 'undefined') {
      return fallback;
    }

    var val = null;

    if (type === 'projects') {
      val = window.SageGridSettings.projectsPageSize;
    } else if (type === 'blog') {
      val = window.SageGridSettings.blogPageSize;
    }

    var parsed = parseInt(val, 10);
    if (!isNaN(parsed) && parsed > 0) {
      return parsed;
    }

    return fallback;
  }

  function initFilterGrid(options) {
    const cardSelector = options.cardSelector;
    const termKey      = options.termKey;      // dataset key: "tech" or "category"
    const loadMoreId   = options.loadMoreId;   // button id
    const PAGE_SIZE    = options.pageSize;     // <<< new

    const buttons     = document.querySelectorAll('.filter-btn[data-filter]');
    const cards       = Array.from(document.querySelectorAll(cardSelector));
    const loadMoreBtn = document.getElementById(loadMoreId);

    // If this grid doesn't exist on the page, do nothing
    if (!cards.length) {
      return;
    }

    let currentFilter = 'all';
    let visibleCount  = PAGE_SIZE;

    function getMatchingCards() {
      return cards.filter(card => {
        const raw  = card.dataset[termKey] || '';
        const list = raw.split(' ').filter(Boolean);
        return currentFilter === 'all' || list.includes(currentFilter);
      });
    }

    function updateUI() {
      const matching = getMatchingCards();

      if (visibleCount > matching.length) {
        visibleCount = matching.length;
      }

      // Hide all cards
      cards.forEach(card => card.classList.add('hidden'));

      // Show first N matching
      matching.slice(0, visibleCount).forEach(card => {
        card.classList.remove('hidden');
      });

      // Toggle Load More visibility
      if (loadMoreBtn) {
        if (matching.length > visibleCount) {
          loadMoreBtn.style.display = 'inline-flex';
        } else {
          loadMoreBtn.style.display = 'none';
        }
      }
    }

    // Filter chip click
    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        const filter = btn.dataset.filter || 'all';
        currentFilter = filter;
        visibleCount  = PAGE_SIZE; // reset when filter changes

        buttons.forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');

        updateUI();
      });
    });

    // Load more click
    if (loadMoreBtn) {
      loadMoreBtn.addEventListener('click', () => {
        visibleCount += PAGE_SIZE;
        updateUI();
      });
    }

    // Initial render
    updateUI();
  }

  // Projects grid
  initFilterGrid({
    cardSelector: '.project-card',
    termKey: 'tech',
    loadMoreId: 'projects-load-more',
    pageSize: getPageSize('projects'),
  });

  // Blog grid
  initFilterGrid({
    cardSelector: '.blog-card',
    termKey: 'category',
    loadMoreId: 'blog-load-more',
    pageSize: getPageSize('blog'),
  });

});

