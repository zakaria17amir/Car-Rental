<?php
session_start();
$booking_error = isset($_GET['error']) ? urldecode($_GET['error']) : "An unknown error occurred.";
// Get the error message

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking fail</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header>
        <nav>
            <div class="navbar bg-base-100">
                <div class="flex-1">
                    <a class="btn btn-ghost text-xl bg-yellow-300 text-black" href="index.php">Car Rental</a>
                </div>
                <div class="flex-none gap-2">
                    <ul class="menu menu-horizontal px-1 text-black font-semibold">
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a class="bg-yellow-300" href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a class="bg-yellow-300" href="login.php">Login</a></li>
                            <li><a class="bg-yellow-300" href="registration.php">Registration</a></li>
                        <?php endif; ?>
                    </ul>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                            <div class="w-10 rounded-full">
                                <img
                                    alt="Tailwind CSS Navbar component"
                                    src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                            </div>
                        </div>
                        <ul
                            tabindex="0"
                            class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">
                            <?php if (isset($_SESSION['user'])): ?>
                                <li><a class="justify-between"><?= $_SESSION['user']['full_name'] ?></a></li>
                                <li><a href="profile.php">Profile</a></li>
                                <li><a href="settings.php">Settings</a></li>
                                <li><a href="logout.php">Logout</a></li>
                            <?php else: ?>
                                <li><a href="login.php">Login</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div role="alert" class="alert alert-error">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 shrink-0 stroke-current"
            fill="none"
            viewBox="0 0 24 24">
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Sorry Booking failed <br>
            <?= $booking_error ?> </span>
    </div>

</body>

</html>