<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "userprofile");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = $_SESSION['email']; // Ambil email dari sesi

    if (!empty($firstname) && !empty($lastname)) {
        // Persiapkan query untuk update data pengguna
        $query = "UPDATE user SET firstname = ?, lastname = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        $stmt->bind_param("sss", $firstname, $lastname, $email);

        if ($stmt->execute()) {
            // Update session data setelah berhasil memperbarui profil
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;

            $_SESSION['profile_update'] = "Profil berhasil diperbarui!"; // Menyimpan pesan sukses ke session
        } else {
            $_SESSION['profile_update'] = "Gagal memperbarui profil: " . $stmt->error; // Menyimpan pesan gagal ke session
        }

        $stmt->close();
    } else {
        $_SESSION['profile_update'] = "Semua kolom harus diisi!"; // Menyimpan pesan validasi ke session
    }
}

mysqli_close($conn);

// Redirect ke halaman catalog.php
header("Location: catalog.php");
exit();
?>
