import.meta.glob([
  '../images/**',
  '../fonts/**',
]);











document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('.filter-btn');
  const cards   = Array.from(document.querySelectorAll('.project-card'));
  const loadMoreBtn = document.getElementById('projects-load-more');
  const PAGE_SIZE = 6;

  if (!cards.length) return;

  let currentFilter = 'all';
  let visibleCount  = PAGE_SIZE;

  function applyFilter() {
    // Which cards match the current filter?
    const matching = cards.filter(card => {
      const tech = card.dataset.tech
        ? card.dataset.tech.split(' ').filter(Boolean)
        : [];
      return currentFilter === 'all' || tech.includes(currentFilter);
    });

    // Hide everything
    cards.forEach(card => card.classList.add('hidden'));

    // Show only first N matching
    matching.slice(0, visibleCount).forEach(card => {
      card.classList.remove('hidden');
    });

    // Toggle Load More button
    if (loadMoreBtn) {
      if (matching.length > visibleCount) {
        loadMoreBtn.classList.remove('hidden');
      } else {
        loadMoreBtn.classList.add('hidden');
      }
    }
  }

  // Filter button click
  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.dataset.filter || 'all';
      currentFilter = filter;
      visibleCount  = PAGE_SIZE; // reset when filter changes

      buttons.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');

      applyFilter();
    });
  });

  // Load more click
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', () => {
      visibleCount += PAGE_SIZE;
      applyFilter();
    });
  }

  // Initial state
  applyFilter();
});
