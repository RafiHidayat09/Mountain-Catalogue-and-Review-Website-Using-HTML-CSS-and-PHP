<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'rafiadmin@unesa.com.id') {
    // Redirect ke halaman login jika belum login atau bukan admin
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "userprofile");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Hitung total pengguna
$total_users_sql = "SELECT COUNT(*) AS total FROM user";
$total_users_result = mysqli_query($conn, $total_users_sql);
$total_users_row = mysqli_fetch_assoc($total_users_result);
$total_users = $total_users_row['total'];

// Ambil semua data pengguna
$sql = "SELECT * FROM user";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - RafiAdmin</title>
    <link rel="stylesheet" href="halamanadmin.css">
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
            <h1>Admin Panel - RafiAdmin</h1>
        </header>

        <div class="dashboard-info">
            <div class="info-box">
                <i class="fas fa-users fa-3x"></i>
                <h3>Total Pengguna</h3>
                <p><?php echo $total_users; ?> pengguna</p>
            </div>
        </div>

        <h2>Daftar Pengguna</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Id']); ?></td>
                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['password']); ?></td>
                        <td>
                            <!-- Form Update -->
                            <form method="POST" action="halamanadmin.php" style="display:inline;">
                                <input type="hidden" name="Id" value="<?php echo htmlspecialchars($row['Id']); ?>">
                                <input type="text" name="firstname" value="<?php echo htmlspecialchars($row['firstname']); ?>" required>
                                <input type="text" name="lastname" value="<?php echo htmlspecialchars($row['lastname']); ?>" required>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                <button type="submit" name="update_user" class="btn-update">Ubah</button>
                            </form>
                            <!-- Tombol Hapus -->
                            <form method="GET" action="halamanadmin.php" style="display:inline;">
                                <input type="hidden" name="delete" value="<?php echo htmlspecialchars($row['Id']); ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</button>
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
