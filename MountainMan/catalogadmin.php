<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'rafiadmin@unesa.com.id') {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Fungsi untuk menangani form tambah, edit, dan hapus gunung
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Menambah data gunung baru
        $mountain_name = mysqli_real_escape_string($conn, $_POST['mountain_name']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
        $time = mysqli_real_escape_string($conn, $_POST['time']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $maps_url = mysqli_real_escape_string($conn, $_POST['maps_url']);
        $distance = mysqli_real_escape_string($conn, $_POST['distance']);
        $height = mysqli_real_escape_string($conn, $_POST['height']);
        $route_type = mysqli_real_escape_string($conn, $_POST['route_type']);

        // Mengambil file gambar dan menyimpannya
        $mountainimage = '';
        if (isset($_FILES['mountainimage']) && $_FILES['mountainimage']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["mountainimage"]["name"]);
            if (move_uploaded_file($_FILES["mountainimage"]["tmp_name"], $target_file)) {
                $mountainimage = basename($_FILES["mountainimage"]["name"]);
            }
        }

        $query = "INSERT INTO mountains (mountain_name, location, difficulty, time, mountainimage, description, maps_url, distance, height, route_type)
                  VALUES ('$mountain_name', '$location', '$difficulty', '$time', '$mountainimage', '$description', '$maps_url', '$distance', '$height', '$route_type')";
        if (!mysqli_query($conn, $query)) {
            die("Error: " . mysqli_error($conn));
        }
    }

    if (isset($_POST['update'])) {
        // Mengubah data gunung berdasarkan ID
        $id = (int)$_POST['id'];
        $mountain_name = mysqli_real_escape_string($conn, $_POST['mountain_name']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
        $time = mysqli_real_escape_string($conn, $_POST['time']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $maps_url = mysqli_real_escape_string($conn, $_POST['maps_url']);
        $distance = mysqli_real_escape_string($conn, $_POST['distance']);
        $height = mysqli_real_escape_string($conn, $_POST['height']);
        $route_type = mysqli_real_escape_string($conn, $_POST['route_type']);

        $mountainimage = $_POST['old_image'];
        if (isset($_FILES['mountainimage']) && $_FILES['mountainimage']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["mountainimage"]["name"]);
            if (move_uploaded_file($_FILES["mountainimage"]["tmp_name"], $target_file)) {
                $mountainimage = basename($_FILES["mountainimage"]["name"]);
            }
        }

        $query = "UPDATE mountains SET 
                  mountain_name='$mountain_name',
                  location='$location',
                  difficulty='$difficulty',
                  time='$time',
                  mountainimage='$mountainimage',
                  description='$description',
                  maps_url='$maps_url',
                  distance='$distance',
                  height='$height',
                  route_type='$route_type'
                  WHERE Id_mountain=$id";
        if (!mysqli_query($conn, $query)) {
            die("Error: " . mysqli_error($conn));
        }
    }

    if (isset($_POST['delete'])) {
        // Menghapus data gunung berdasarkan ID
        $id = (int)$_POST['id'];
        $query = "DELETE FROM mountains WHERE Id_mountain=$id";
        if (!mysqli_query($conn, $query)) {
            die("Error: " . mysqli_error($conn));
        }
    }

    // Redirect untuk menghindari resubmission form
    header("Location: catalog.php");
    exit();
}

// Ambil semua data gunung
$query = "SELECT * FROM mountains";
$result = mysqli_query($conn, $query);
$mountains = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gunung</title>
    <link rel="stylesheet" href="catalogadmin.css">
</head>
<body>

<header>
    <div class="container">
        <a href="" class="logo">Admin Mountain Man</a>
        <nav>
            <ul>
                <li><a href="halamanadmin.php">Admin Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <h2>Admin Gunung</h2>

    <!-- Form Tambah Gunung -->
    <h3>Tambah Gunung</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="mountain_name" placeholder="Nama Gunung" required>
        <input type="text" name="location" placeholder="Lokasi" required>
        <select name="difficulty" required>
            <option value="Easy">Easy</option>
            <option value="Mid-Hard">Mid-Hard</option>
            <option value="Hard">Hard</option>
        </select>
        <input type="text" name="time" placeholder="Waktu" required>
        <input type="file" name="mountainimage" accept="image/*">
        <textarea name="description" placeholder="Deskripsi" required></textarea>
        <input type="text" name="maps_url" placeholder="URL Peta Google Maps" required>
        <input type="text" name="distance" placeholder="Jarak (km)" required>
        <input type="text" name="height" placeholder="Ketinggian (m)" required>
        <input type="text" name="route_type" placeholder="Jenis Rute" required>
        <button type="submit" name="add">Tambah Gunung</button>
    </form>

    <!-- Daftar Gunung -->
    <h3>Daftar Gunung</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Gunung</th>
                <th>Lokasi</th>
                <th>Kesulitan</th>
                <th>Waktu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mountains as $mountain): ?>
                <tr>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $mountain['Id_mountain'] ?>">
                        <td><input type="text" name="mountain_name" value="<?= htmlspecialchars($mountain['mountain_name']) ?>" required></td>
                        <td><input type="text" name="location" value="<?= htmlspecialchars($mountain['location']) ?>" required></td>
                        <td>
                            <select name="difficulty" required>
                                <option value="Easy" <?= $mountain['difficulty'] == 'Easy' ? 'selected' : '' ?>>Easy</option>
                                <option value="Mid-Hard" <?= $mountain['difficulty'] == 'Mid-Hard' ? 'selected' : '' ?>>Mid-Hard</option>
                                <option value="Hard" <?= $mountain['difficulty'] == 'Hard' ? 'selected' : '' ?>>Hard</option>
                            </select>
                        </td>
                        <td><input type="text" name="time" value="<?= htmlspecialchars($mountain['time']) ?>" required></td>
                        <td>
                            <input type="hidden" name="old_image" value="<?= $mountain['mountainimage'] ?>">
                            <input type="file" name="mountainimage" accept="image/*">
                            <button type="submit" name="update">Update</button>
                            <button type="submit" name="delete" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

</body>
</html>
