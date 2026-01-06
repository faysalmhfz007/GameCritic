(() => {
  const input = document.getElementById('searchInput');
  const dropdown = document.getElementById('searchDropdown');
  if (!input || !dropdown) return;

  const base = (window.__BASE_URL__ || '').replace(/\/$/, '');

  input.addEventListener('input', () => {
    const query = input.value.trim();
    if (query.length < 2) {
      dropdown.innerHTML = '';
      dropdown.style.display = 'none';
      return;
    }
    fetch(`${base}/search?term=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        if (!Array.isArray(data) || data.length === 0) {
          dropdown.innerHTML = '<div class="list-group-item">No results found</div>';
          dropdown.style.display = 'block';
          return;
        }
        dropdown.innerHTML = data.map(game =>
          `<a href="${base}/game/${game.id}" class="list-group-item list-group-item-action">${game.title}</a>`
        ).join('');
        dropdown.style.display = 'block';
      })
      .catch(() => {
        dropdown.innerHTML = '<div class="list-group-item text-danger">Error loading results</div>';
        dropdown.style.display = 'block';
      });
  });

  document.addEventListener('click', e => {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });
})();


