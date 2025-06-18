<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "userprofile");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Proses pendaftaran
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['SignUp'])) {
    // Ambil data dari form
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Validasi input
    if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($password)) {
        // Pastikan password tidak kosong dan sesuai
        $password = mysqli_real_escape_string($conn, $password);

        // Persiapkan query untuk memasukkan data pengguna
        $query_insert = "INSERT INTO user (firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query_insert);
        if ($stmt) {
            // Bind parameter dan eksekusi
            $stmt->bind_param("ssss", $firstname, $lastname, $email, $password);
            if ($stmt->execute()) {
                // Redirect jika pendaftaran berhasil
                header("Location: catalog.php");
                exit();
            } else {
                echo "Pendaftaran gagal: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Query gagal: " . mysqli_error($conn);
        }
    } else {
        echo "Semua kolom harus diisi!";
    }
}

// Proses login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['SignIn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input
    if (!empty($email) && !empty($password)) {
        // Persiapkan query untuk memeriksa kecocokan email dan password
        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            // Bind parameter dan eksekusi
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Cek password
                if ($password === $row['password']) {
                    // Cek apakah ini admin atau user biasa
                    session_start();
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['user_id'] = $row['Id'];
                    // Jika email dan password adalah untuk admin
                    if ($row['email'] === 'rafiadmin@unesa.com.id' && $row['password'] === 'rafiadmin') {
                        // Redirect ke halaman admin
                        header("Location: halamanadmin.php");
                    } else {
                        // Redirect ke halaman catalog untuk user biasa
                        header("Location: catalog.php");
                    }
                    exit();
                } else {
                    echo "Password salah!";
                }
            } else {
                echo "Email tidak ditemukan!";
            }
            $stmt->close();
        } else {
            echo "Query gagal: " . mysqli_error($conn);
        }
    } else {
        echo "Email dan password harus diisi!";
    }
}

// Tutup koneksi
mysqli_close($conn);
?>
