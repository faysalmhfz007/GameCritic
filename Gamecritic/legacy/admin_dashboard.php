<?php
session_start();
require __DIR__ . '/db/db.php';



if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}


if (isset($_GET['delete'])) {
    $game_id = intval($_GET['delete']);


    $stmt = $conn->prepare("SELECT cover_image FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $game = $result->fetch_assoc();
        if ($game['cover_image'] && file_exists('uploads/' . $game['cover_image'])) {
            unlink('uploads/' . $game['cover_image']);
        }
    }


    $stmt = $conn->prepare("DELETE FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $platform = $_POST['platform'] ?? '';
    $release_year = intval($_POST['release_year'] ?? 0);
    $description = $_POST['description'] ?? '';
    $cover_image = '';


    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cover_image']['tmp_name'];
        $fileName = basename($_FILES['cover_image']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));


        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed_ext)) {
            $newFileName = uniqid() . '.' . $ext;
            $dest_path = 'uploads/' . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $cover_image = $newFileName;
            }
        }
    }


    $stmt = $conn->prepare("INSERT INTO games (title, genre, platform, release_year, description, cover_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $title, $genre, $platform, $release_year, $description, $cover_image);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit();
}


$result = $conn->query("SELECT * FROM games ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard | GameCritic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">GameCritic Admin</a>
    <div class="d-flex">
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

  <h2>Add New Game</h2>
  <form method="POST" enctype="multipart/form-data" class="mb-4">
    <div class="mb-3">
      <label for="title" class="form-label">Game Title</label>
      <input type="text" class="form-control" id="title" name="title" required />
    </div>

    <div class="mb-3">
      <label for="genre" class="form-label">Genre</label>
      <input type="text" class="form-control" id="genre" name="genre" required />
    </div>

    <div class="mb-3">
      <label for="platform" class="form-label">Platform</label>
      <input type="text" class="form-control" id="platform" name="platform" required />
    </div>

    <div class="mb-3">
      <label for="release_year" class="form-label">Release Year</label>
      <input type="number" class="form-control" id="release_year" name="release_year" min="1950" max="2050" required />
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>

    <div class="mb-3">
      <label for="cover_image" class="form-label">Cover Image</label>
      <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*" />
    </div>

    <button type="submit" class="btn btn-danger">Add Game</button>
  </form>

  <h2>Existing Games</h2>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Cover</th>
        <th>Title</th>
        <th>Genre</th>
        <th>Platform</th>
        <th>Year</th>
        <th>Description</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($game = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($game['id']) ?></td>
          <td>
            <?php if ($game['cover_image'] && file_exists('uploads/' . $game['cover_image'])): ?>
              <img src="uploads/<?= htmlspecialchars($game['cover_image']) ?>" alt="Cover" style="height: 60px;">
            <?php else: ?>
              No Image
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($game['title']) ?></td>
          <td><?= htmlspecialchars($game['genre']) ?></td>
          <td><?= htmlspecialchars($game['platform']) ?></td>
          <td><?= htmlspecialchars($game['release_year']) ?></td>
          <td><?= nl2br(htmlspecialchars($game['description'])) ?></td>
          <td>
            <a href="admin_dashboard.php?delete=<?= $game['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this game?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
