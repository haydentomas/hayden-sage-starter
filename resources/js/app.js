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




// TOC

document.addEventListener('DOMContentLoaded', () => {
  const tocCard = document.getElementById('post-toc-card');
  const tocContainer = document.getElementById('post-toc');
  const content = document.querySelector('.e-content');

  if (!tocCard || !tocContainer || !content) {
    return; // not a single post or no content
  }

  // Collect headings (h2 + h3 is a nice balance)
  const headings = Array.from(content.querySelectorAll('h2, h3'));

  if (!headings.length) {
    // No headings → hide the card entirely
    tocCard.style.display = 'none';
    return;
  }

  // Simple slug/ID generator
  function makeSlug(text) {
    return text
      .toLowerCase()
      .trim()
      .replace(/[^\w]+/g, '-')   // non-word to dash
      .replace(/^-+|-+$/g, '');  // trim dashes
  }

  // Build TOC list
  const list = document.createElement('ul');

  headings.forEach((heading) => {
    if (!heading.id) {
      heading.id = makeSlug(heading.textContent);
    }

    const li = document.createElement('li');
    const link = document.createElement('a');

    link.href = `#${heading.id}`;
    link.textContent = heading.textContent || heading.innerText || '';

    // Mark level so we can indent h3s in CSS
    if (heading.tagName.toLowerCase() === 'h3') {
      li.classList.add('toc-level-3');
    }

    li.appendChild(link);
    list.appendChild(li);
  });

  // Clear placeholder text and inject list
  tocContainer.innerHTML = '';
  tocContainer.appendChild(list);
});




(() => {
  const header = document.getElementById('site-header');
  if (!header) return;
  if (!header.classList.contains('is-sticky')) return;

  const offset = 10; // px before we consider it "scrolled"

  const setState = () => {
    if (window.scrollY > offset) {
      header.classList.add('is-sticky-scrolled');
    } else {
      header.classList.remove('is-sticky-scrolled');
    }
  };

  // init + update
  setState();
  window.addEventListener('scroll', setState, { passive: true });
})();
