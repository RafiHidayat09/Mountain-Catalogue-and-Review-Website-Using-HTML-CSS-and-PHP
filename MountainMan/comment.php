<?php
session_start();

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "userprofile");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menangani data form komentar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
    $rating = $_POST['rating'];
    $user_id = $_SESSION['user_id']; // Ambil user_id dari session yang sudah login
    $mountain_id = $_POST['mountain_id']; // Ambil mountain_id dari form

    
    // Query untuk menyimpan komentar
    $query = "INSERT INTO comment (comment_text, rating, user_id, mountain_id) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'siii', $comment_text, $rating, $user_id, $mountain_id);

    if (mysqli_stmt_execute($stmt)) {
        // Mengirimkan data dalam format JSON agar bisa digunakan oleh AJAX
        $response = [
            'comment_text' => $comment_text,
            'rating' => $rating,
            'created_at' => date('Y-m-d H:i:s')  // Menambahkan waktu saat komentar dibuat
        ];
        echo json_encode($response); // Mengembalikan respons ke AJAX
    } else {
        echo json_encode(['error' => 'Gagal menyimpan komentar']);
    }

    mysqli_stmt_close($stmt);
}

// Menutup koneksi
mysqli_close($conn);
?>
