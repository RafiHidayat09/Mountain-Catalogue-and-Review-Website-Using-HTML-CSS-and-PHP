<?php
session_start();

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "userprofile");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil ID gunung dari parameter URL
$mountain_id = isset($_GET['Id_mountain']) ? (int)$_GET['Id_mountain'] : 0;

// Ambil data gunung berdasarkan ID
$query = "SELECT * FROM mountains WHERE Id_mountain = $mountain_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

$mountain = mysqli_fetch_assoc($result);

if (!$mountain) {
    // Jika data gunung tidak ditemukan
    die("Gunung tidak ditemukan.");
}

$location = $mountain['location']; // Ambil lokasi gunung untuk cuaca

// Menutup koneksi ke database

if (!isset($_SESSION['user_id'])) {
    die("User tidak ditemukan. Anda harus login terlebih dahulu.");
}
$user_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($mountain['mountain_name']) ?> - Deskripsi</title>
    <link rel="icon" href="faviconku.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="deskripsigunung.css">

</head>
<body>

<!-- Navbar -->
<header>
    <div class="container">
        <a href="" class="logo">
            <img src="logo_gunung.png" alt="Logo Mountain Man">
        </a>
        <nav>
            <ul>
                <li><a href="">Home</a></li>
                <li><a href="">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="updateprofile.php"><i class="fa-solid fa-user"></i></a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <!-- Deskripsi Gunung -->
    <div class="mountain-description">
        <h2><?= htmlspecialchars($mountain['mountain_name']) ?></h2>
        <div class="mountain-image" style="background-image: url('<?= htmlspecialchars($mountain['mountainimage']) ?>');"></div>
        <p><strong>Kesulitan:</strong> <?= htmlspecialchars($mountain['difficulty']) ?></p>
        <p><strong>Waktu untuk Mencapai Puncak:</strong> <?= htmlspecialchars($mountain['time']) ?></p>
        <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($mountain['description'])) ?></p>
    </div>

    <!-- Peta Google Maps -->
    <?php if (!empty($mountain['maps_url'])): ?>
        <div class="peta">
            <iframe class="peta_frame" 
                    src="<?= htmlspecialchars($mountain['maps_url']) ?>" 
                    width="600" height="450" 
                    frameborder="0" 
                    style="border:0" 
                    allowfullscreen="" 
                    loading="lazy"></iframe>
        </div>
    <?php else: ?>
        <p>Peta tidak tersedia untuk lokasi ini.</p>
    <?php endif; ?>

    <!-- Informasi Cuaca -->
    <div id="weatherResult"></div>

    <script>
    // API Cuaca
    const apiKey = 'ba857300bdc774a44b98bf9a7ad3c97a';

    window.onload = () => {
        const city = '<?= $location ?>';
        if (city) {
            getWeather(city);
        } else {
            document.getElementById('weatherResult').innerText = 'Data lokasi tidak tersedia.';
        }
    };

    async function getWeather(city) {
        try {
            const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`);
            if (!response.ok) {
                throw new Error('Lokasi tidak ditemukan');
            }
            const data = await response.json();
            displayWeather(data);
        } catch (error) {
            document.getElementById('weatherResult').innerText = error.message;
        }
    }

    function displayWeather(data) {
        const weatherResult = document.getElementById('weatherResult');
        const temperature = data.main.temp;
        const weatherDescription = data.weather[0].description;
        const city = data.name;
        const humidity = data.main.humidity;
        const windSpeed = data.wind.speed;
        
        // Ambil ikon cuaca dari OpenWeatherMap
        const iconCode = data.weather[0].icon;
        const iconUrl = `https://openweathermap.org/img/wn/${iconCode}.png`;

        // Tampilkan hasil dengan ikon cuaca
        weatherResult.innerHTML = `
            <div class="weather-info">
                <h3>Cuaca di ${city}</h3>
                <img src="${iconUrl}" alt="Ikon Cuaca" class="weather-icon">
                <p class="temperature">Suhu: <strong>${temperature} °C</strong></p>
                <p class="description_con">Kondisi: <strong>${weatherDescription}</strong></p>
                <p>Kelembaban: ${humidity}%</p>
                <p>Kecepatan Angin: ${windSpeed} m/s</p>
            </div>
        `;
    }
    </script>
     <!-- Form Komentar -->
    <!-- Form Komentar -->
    <div class="comment-section">
        <h3>Tambah Komentar</h3>
        <form id="commentForm" action="comment.php" method="post">
            <textarea name="comment_text" rows="4" cols="50" required placeholder="Tulis komentar di sini..."></textarea><br>
            <label for="rating">Rating (1-5):</label>
            <select name="rating" required>
                <option value="1">1 Bintang</option>
                <option value="2">2 Bintang</option>
                <option value="3">3 Bintang</option>
                <option value="4">4 Bintang</option>
                <option value="5">5 Bintang</option>
            </select><br><br>
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? '' ?>">
            <input type="hidden" name="mountain_id" value="<?= $mountain_id ?>">
            <input type="submit" value="Kirim Komentar">
        </form>
    </div>

    <!-- Daftar Komentar -->
    <div class="comments-list">
        <h3>Komentar</h3>
        <?php
        // Ambil komentar dari database berdasarkan mountain_id
        $query = "SELECT comment.*, user.firstname, user.lastname 
                  FROM comment
                  JOIN user ON comment.user_id = user.Id
                  WHERE comment.mountain_id = $mountain_id
                  ORDER BY comment.created_at DESC";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Query gagal: " . mysqli_error($conn));
        }

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='comment-item'>";
            echo "<strong>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . "</strong><br>";
            echo "<p>" . nl2br(htmlspecialchars($row['comment_text'])) . "</p>";
            echo "<p>Rating: ";
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $row['rating']) {
                    echo "&#9733;"; // Bintang terisi
                } else {
                    echo "&#9734;"; // Bintang kosong
                }
            }
            echo "</p>";
            echo "<p><small>Dikirim pada: " . htmlspecialchars($row['created_at']) . "</small></p>";
            echo "</div><hr>";
        }
        ?>
    </div>

</main>
<!-- Footer -->
<footer>
    <h3>Get in touch</h3>
    <ul class="footericon">
        <li><a href=""><i class="fa-brands fa-github"></i></a></li>
        <li><a href=""><i class="fa-brands fa-facebook"></i></a></li>
        <li><a href=""><i class="fa-brands fa-instagram"></i></a></li>
        <li><a href=""><i class="fa-brands fa-whatsapp"></i></a></li>
        <li><a href=""><i class="fa-brands fa-x-twitter"></i></a></li>
    </ul>
    <p>Created by Muhammad Rafi Hidayat @UNESA</p>
</footer>
<!-- Modal Popup -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Profil Pengguna</h2>
        <form id="profileForm" method="POST" action="update_profile.php">
            <label for="firstname">Firstname:</label>
            <input type="text" id="firstname" name="firstname" value="" required>

            <label for="lastname">Lastname:</label>
            <input type="text" id="lastname" name="lastname" value="" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="" readonly>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    transition: opacity 0.3s ease;
}

.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 30px;
    width: 50%;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.close {
    font-size: 30px;
    font-weight: bold;
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 20px;
    cursor: pointer;
}

.close:hover {
    color: black;
}

input[type="text"], input[type="email"], button[type="submit"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 16px;
}

input[type="text"]:focus, input[type="email"]:focus {
    border-color: #4CAF50;
}

button[type="submit"] {
    background-color: #4CAF50;
    color: white;
    border: none;
    font-size: 16px;
    padding: 12px;
    cursor: pointer;
    border-radius: 5px;
}

button[type="submit"]:hover {
    background-color: #45a049;
}
</style>

<script>
// Modal logic
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("profileModal");
    const btn = document.querySelector(".fa-user");
    const span = document.querySelector(".close");

    // Open modal
    btn.addEventListener("click", function (e) {
        e.preventDefault();
        fetchUserProfile();
        modal.style.display = "block";
    });

    // Close modal
    span.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Close modal when clicking outside
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Function to fetch user profile data (example using inline PHP)
    function fetchUserProfile() {
        const firstname = "<?php echo $_SESSION['firstname'] ?? ''; ?>";
        const lastname = "<?php echo $_SESSION['lastname'] ?? ''; ?>";
        const email = "<?php echo $_SESSION['email'] ?? ''; ?>";

        document.getElementById("firstname").value = firstname;
        document.getElementById("lastname").value = lastname;
        document.getElementById("email").value = email;
    }
});
</script>
</body>
</html>