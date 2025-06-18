<?php 
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'rafiadmin@unesa.com.id') {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Proses Menambahkan Data Baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_aboutus'])) {
    $name_us = $_POST['name_us'];
    $class = $_POST['class'];
    $nim = $_POST['nim'];
    $description = $_POST['description'];
    $profile_img = $_FILES['profile_img']['name'];

    if ($profile_img) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_img"]["name"]);
        move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_file);
    }

    $insert_query = "INSERT INTO aboutus (name_us, class, nim, description, profile_img) 
                     VALUES ('$name_us', '$class', '$nim', '$description', '$profile_img')";
    
    if (mysqli_query($conn, $insert_query)) {
        echo "Data berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
}

// Ambil semua data aboutus
$query = "SELECT * FROM aboutus";
$result = mysqli_query($conn, $query);

// Proses Update Data
if (isset($_POST['update_aboutus'])) {
    $id = $_POST['id_aboutus'];
    $name_us = $_POST['name_us'];
    $class = $_POST['class'];
    $nim = $_POST['nim'];
    $description = $_POST['description'];
    $profile_img = $_FILES['profile_img']['name'];

    if ($profile_img) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_img"]["name"]);
        move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_file);
        $update_query = "UPDATE aboutus SET name_us = '$name_us', class = '$class', nim = '$nim', description = '$description', profile_img = '$profile_img' WHERE id_aboutus = '$id'";
    } else {
        $update_query = "UPDATE aboutus SET name_us = '$name_us', class = '$class', nim = '$nim', description = '$description' WHERE id_aboutus = '$id'";
    }

    if (mysqli_query($conn, $update_query)) {
        echo "Data berhasil diperbarui!";
    } else {
        echo "Gagal memperbarui data: " . mysqli_error($conn);
    }
}

// Proses Hapus Data
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM aboutus WHERE id_aboutus = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        echo "Data berhasil dihapus!";
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - About Us</title>
    <link rel="stylesheet" href="assets/css/aboutusadmin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Style untuk Sidebar */
        body {
            display: flex;
        }
        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: white;
            padding-top: 20px;
            height: 100vh;
            position: fixed;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
            margin: 10px 0;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2 style="text-align: center; color: white;">Admin Panel</h2>
        <a href="halamanadmin.php">Dashboard</a>
        <a href="aboutusadmin.php">About Us</a>
        <a href="catalogadmin.php">Catalog Gunung</a>
        <form action="logout.php" method="post" style="text-align: center;">
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>

    <div class="main-content">
        <header>
            <h1>Admin Panel - About Us</h1>
        </header>

        <h2>Tambah Data About Us</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name_us" placeholder="Nama Lengkap" required><br>
            <input type="text" name="class" placeholder="Kelas" required><br>
            <input type="text" name="nim" placeholder="NIM" required><br>
            <textarea name="description" placeholder="Deskripsi" required></textarea><br>
            <input type="file" name="profile_img"><br>
            <input type="submit" name="add_aboutus" value="Tambah Data">
        </form>

        <h2>Data About Us</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>NIM</th>
                    <th>Deskripsi</th>
                    <th>Gambar Profil</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <form method="POST" action="aboutusadmin.php" enctype="multipart/form-data">
                            <td><input type="text" name="name_us" value="<?php echo htmlspecialchars($row['name_us']); ?>" required></td>
                            <td><input type="text" name="class" value="<?php echo htmlspecialchars($row['class']); ?>" required></td>
                            <td><input type="text" name="nim" value="<?php echo htmlspecialchars($row['nim']); ?>" required></td>
                            <td><textarea name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea></td>
                            <td><input type="file" name="profile_img"></td>
                            <td>
                                <input type="hidden" name="id_aboutus" value="<?php echo htmlspecialchars($row['id_aboutus']); ?>">
                                <button type="submit" name="update_aboutus" class="btn-update">Ubah</button>
                            </td>
                        </form>
                        <td>
                            <!-- Tombol Hapus -->
                            <form method="GET" action="aboutusadmin.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($row['id_aboutus']); ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>
