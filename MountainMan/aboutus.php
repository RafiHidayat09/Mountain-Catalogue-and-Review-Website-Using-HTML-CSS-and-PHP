<?php
session_start();

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "userprofile");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil data dari tabel 'aboutus'
$query = "SELECT * FROM aboutus";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="catalog.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
    
        .catalog {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
        }
        .card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            width: 30%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .card img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .card h3 {
            margin-bottom: 0.5rem;
            color: #4CAF50;
        }
        .card p {
            margin: 0.5rem 0;
            color: #555;
        }
    </style>
</head>
<body>
    <header>
    <header>
    <div class="container">
        <a href="" class="logo">
            <img src="logo_gunung.png" alt="Mountain Man Logo">
        </a>
        <nav>
            <ul>
                <li><a href="">Home</a></li>
                <li><a href="">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="updateprofile.php"><i class="fa-solid fa-user"></i></a>
            </li>
            </ul>
        </nav>
    </div>
</header>

    </header>

    <div class="container">
        <div class="catalog">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="card">
                    <?php if (!empty($row['profile_img'])): ?>
                        <img src="<?= htmlspecialchars($row['profile_img']) ?>" alt="Profile Image of <?= htmlspecialchars($row['name_us']) ?>">
                    <?php else: ?>
                        <img src="default-image.jpg" alt="Default Image">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($row['name_us']) ?></h3>
                    <p><strong>Kelas:</strong> <?= htmlspecialchars($row['class']) ?></p>
                    <p><strong>NIM:</strong> <?= htmlspecialchars($row['nim']) ?></p>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php
    
    ?>
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
        <form id="profileForm" method="POST" action="updateprofile.php">
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

<?php
// Menutup koneksi ke database
mysqli_close($conn);
?>

</body>
</html>
