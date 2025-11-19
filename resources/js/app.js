import.meta.glob([
  '../images/**',
  '../fonts/**',
]);











document.addEventListener('DOMContentLoaded', () => {
  // Only buttons that have a data-filter attribute are real filters
  const buttons     = document.querySelectorAll('.filter-btn[data-filter]');
  const cards       = Array.from(document.querySelectorAll('.project-card'));
  const loadMoreBtn = document.getElementById('projects-load-more');
  const PAGE_SIZE   = 6;

  if (!cards.length) return;

  let currentFilter = 'all';
  let visibleCount  = PAGE_SIZE;

  function getMatchingCards() {
    return cards.filter(card => {
      const tech = card.dataset.tech
        ? card.dataset.tech.split(' ').filter(Boolean)
        : [];
      return currentFilter === 'all' || tech.includes(currentFilter);
    });
  }

  function updateUI() {
    const matching = getMatchingCards();

    // Clamp visibleCount so we never go past total
    if (visibleCount > matching.length) {
      visibleCount = matching.length;
    }

    // Hide all cards
    cards.forEach(card => card.classList.add('hidden'));

    // Show only first N matching cards
    matching.slice(0, visibleCount).forEach(card => {
      card.classList.remove('hidden');
    });

    // Toggle Load More button
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
});
