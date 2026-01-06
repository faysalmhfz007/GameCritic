<?php
session_start();
require_once "db/db.php";

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : "";


if ($searchQuery !== "") {
    $stmt = $conn->prepare("SELECT * FROM games WHERE name ILIKE ?");
    $likeQuery = '%' . $searchQuery . '%';
    $stmt->bind_param("s", $likeQuery);
} else {
    $stmt = $conn->prepare("SELECT * FROM games");
}

$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>GameCritic – Discover Games</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css" />
  <style>

    form.d-flex { position: relative; }
    #searchDropdown {
      width: 100%;
      max-height: 200px;
      overflow-y: auto;
      cursor: pointer;
    }
  </style>
</head>
<body>

<!-- 🔝 Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">🎮 GameCritic</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Genres</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Platforms</a></li>
      </ul>
      <form class="d-flex me-2" autocomplete="off">
        <input id="searchInput" class="form-control me-2" type="search" placeholder="Search games...">
        <button class="btn btn-outline-light" type="submit">Search</button>
        <div id="searchDropdown" class="list-group position-absolute bg-white text-dark"></div>
      </form>

      <?php if (isset($_SESSION['user_email'])): ?>
        <span class="navbar-text text-light me-3">Hello, <?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
        <a href="logout.php" class="btn btn-outline-light">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-danger">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- 🎠 Carousel (Optional) -->
<div id="featuredGames" class="carousel slide mt-3" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="images/godofwar.jpg" class="d-block w-100 carousel-img" alt="Game 1">
      <div class="carousel-caption d-none d-md-block">
        <h5>God of War: Ragnarök</h5>
        <p>Rated 9.8 – Epic Norse adventure continues</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="images/eldenring.jpg" class="d-block w-100 carousel-img" alt="Game 2">
      <div class="carousel-caption d-none d-md-block">
        <h5>Elden Ring</h5>
        <p>FromSoftware's masterpiece of exploration</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="images/zelda.jpg" class="d-block w-100 carousel-img" alt="Game 3">
      <div class="carousel-caption d-none d-md-block">
        <h5>Zelda: Tears of the Kingdom</h5>
        <p>A magical return to Hyrule</p>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#featuredGames" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#featuredGames" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<!-- 🎮 Trending Games (Static) -->
<div class="container mt-5">
  <h2 class="mb-4">Trending Games</h2>
  <div class="row row-cols-1 row-cols-md-3 g-4">
    <div class="col">
      <div class="card h-100">
        <img src="images/spiderman.jpg" class="card-img-top" alt="Spider-Man 2">
        <div class="card-body">
          <h5 class="card-title">Spider-Man 2</h5>
          <p class="card-text">Swing into action with Peter & Miles.</p>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card h-100">
        <img src="images/starfield.jpg" class="card-img-top" alt="Starfield">
        <div class="card-body">
          <h5 class="card-title">Starfield</h5>
          <p class="card-text">Explore the galaxy in Bethesda's new RPG.</p>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card h-100">
        <img src="images/hogwarts.jpg" class="card-img-top" alt="Hogwarts Legacy">
        <div class="card-body">
          <h5 class="card-title">Hogwarts Legacy</h5>
          <p class="card-text">Live the wizarding dream at Hogwarts.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 🎮 All Games (Dynamic from DB) -->
<div class="container mt-5">
  <h2 class="mb-4">All Games</h2>
  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="col">
      <div class="card h-100">
        <img src="<?php echo htmlspecialchars($row['cover_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>">
        <div class="card-body">
          <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
          <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
          <p class="card-text"><small><strong>Genre:</strong> <?php echo htmlspecialchars($row['genre']); ?></small></p>
          <p class="card-text"><small><strong>Platform:</strong> <?php echo htmlspecialchars($row['platform']); ?></small></p>
          <p class="card-text"><small><strong>Released:</strong> <?php echo htmlspecialchars($row['release_year']); ?></small></p>
          <?php if (!empty($row['review'])): ?>
            <p class="card-text"><em><?php echo htmlspecialchars($row['review']); ?></em></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</div>

<!-- 📞 Footer -->
<footer class="bg-dark text-white mt-5 p-4">
  <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
    <div class="mb-3 mb-md-0">
      <strong>GameCritic</strong> © 2025 | All rights reserved.
    </div>
    <div>
      <a href="about.html" class="text-white me-3">About</a>
      <a href="contact.html" class="text-white me-3">Contact</a>
      <a href="https://facebook.com" class="text-white me-2"><img src="images/facebook.png" height="24" alt="Facebook"></a>
      <a href="https://youtube.com" class="text-white"><img src="images/youtube.png" height="24" alt="YouTube"></a>
    </div>
  </div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const searchInput = document.getElementById('searchInput');
  const dropdown = document.getElementById('searchDropdown');

  searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim();
    if (query.length < 2) {
      dropdown.innerHTML = '';
      dropdown.style.display = 'none';
      return;
    }
    fetch(`search_games.php?term=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        if (data.length === 0) {
          dropdown.innerHTML = '<div class="list-group-item">No results found</div>';
          dropdown.style.display = 'block';
          return;
        }
        dropdown.innerHTML = data.map(game =>
          `<a href="game_details.php?id=${game.id}" class="list-group-item list-group-item-action">${game.title}</a>`
        ).join('');
        dropdown.style.display = 'block';
      })
      .catch(() => {
        dropdown.innerHTML = '<div class="list-group-item text-danger">Error loading results</div>';
        dropdown.style.display = 'block';
      });
  });


  document.addEventListener('click', e => {
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });
</script>

</body>
</html>
