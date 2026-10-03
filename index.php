<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book Management System</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<div class="container">


    <!--NAVBAR-->

    <nav class="navbar">

        <a href="index.php" class="logo">
            📚 Book Management
        </a>


        <div class="nav-links">

            <a href="books/index.php">
                Books
            </a>


            <?php if (isset($_SESSION["user_id"])) { ?>

                <?php if ($_SESSION["user_role"] === "admin") { ?>

                    <a href="admin/dashboard.php">
                        Admin Dashboard
                    </a>

                <?php } else { ?>

                    <a href="user/dashboard.php">
                        Dashboard
                    </a>

                <?php } ?>


                <a href="user/favorites.php">
                    ❤️ Favorites
                </a>


                <a href="user/profile.php">
                    Profile
                </a>


                <a href="auth/logout.php">
                    Logout
                </a>


            <?php } else { ?>

                <a href="auth/login.php">
                    Login
                </a>

                <a href="auth/register.php">
                    Register
                </a>

            <?php } ?>

        </div>

    </nav>



    <!--HERO-->

    <section class="hero">

        <?php if (isset($_SESSION["user_id"])) { ?>

            <h1>
                Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?> 👋
            </h1>

            <p>
                Discover, manage and save your favorite books.
            </p>

            <a href="books/index.php" class="btn">
                Browse Books
            </a>

        <?php } else { ?>

            <h1>
                Welcome to Book Management 📚
            </h1>

            <p>
                Discover books, explore new authors and save your favorites.
            </p>

            <a href="books/index.php" class="btn">
                Browse Books
            </a>

            <a href="auth/register.php" class="btn">
                Create Account
            </a>

        <?php } ?>

    </section>



    <!-- FEATURES-->

    <h1>
        What can you do?
    </h1>


    <div class="dashboard-grid">


        <div class="dashboard-card">

            <h2>📚</h2>

            <h3>
                Explore Books
            </h3>

            <p>
                Browse our collection and discover interesting books.
            </p>

            <br>

            <a href="books/index.php" class="btn">
                View Books
            </a>

        </div>



        <div class="dashboard-card">

            <h2>❤️</h2>

            <h3>
                Favorites
            </h3>

            <p>
                Save the books you like and access them later.
            </p>

            <br>

            <?php if (isset($_SESSION["user_id"])) { ?>

                <a href="user/favorites.php" class="btn">
                    My Favorites
                </a>

            <?php } else { ?>

                <a href="auth/login.php" class="btn">
                    Login
                </a>

            <?php } ?>

        </div>



        <div class="dashboard-card">

            <h2>👤</h2>

            <h3>
                Your Profile
            </h3>

            <p>
                Manage your account information.
            </p>

            <br>

            <?php if (isset($_SESSION["user_id"])) { ?>
            <a href="user/profile.php" class="btn">
                    My Profile
                </a>

            <?php } else { ?>

                <a href="auth/register.php" class="btn">
                    Register
                </a>

            <?php } ?>

        </div>


    </div>



    <!--FOOTER-->

    <footer class="footer">

        <p>
            © <?= date("Y") ?> Book Management System
        </p>

        <p>
            Built with PHP, MySQL, HTML, CSS and JavaScript.
        </p>

    </footer>


</div>

<script src="assets/js/script.js"></script>

</body>

</html>