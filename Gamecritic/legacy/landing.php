<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GameCritic – Discover Games</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
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
      <form class="d-flex me-2">
        <input class="form-control me-2" type="search" placeholder="Search games...">
        <button class="btn btn-outline-light" type="submit">Search</button>
      </form>

<a href="login.php" class="btn btn-danger">Login</a>

    </div>
  </div>
</nav>

<!-- 🎠 Carousel -->
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
  <button class="carousel-control-next" type="button"s data-bs-target="#featuredGames" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<!-- 🎮 Game List -->
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
</body>
</html>