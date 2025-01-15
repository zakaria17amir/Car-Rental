<?php
session_start();
include_once('storage.php');

$cars_storage = new JsonIO('cars.json');
$cars = $cars_storage->load();

$bookings_storage = new JsonIO('bookings.json');
$bookings = $bookings_storage->load();


$user_email = $_SESSION['user']['email'];
$booking = null;

// Find the most recent booking for the logged-in user
foreach (array_reverse($bookings) as $b) {  // Iterate in reverse to find the latest booking
    if ($b['user_email'] === $user_email) {
        $booking = $b;
        break;
    }
}

// Find the booked car details
$booked_car = null;
if ($booking) {
    foreach ($cars as $car) {
        if ($car['id'] == $booking['car_id']) {
            $booked_car = $car;
            break;
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
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
                                <?php $profileLink = $_SESSION['user']['admin_status'] ? "admin_profile.php" : "user_profile.php"; ?>
                                <li><a href="<?= $profileLink ?>">Profile</a></li>
                                <li><a href="#">Settings</a></li>
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
    <?php if ($booking && $booked_car): ?>
        <div role="alert" class="alert alert-success flex-row">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 shrink-0 stroke-current"
                fill="none"
                viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Your booking has been confirmed!</span>
            <h2>Booking Details</h2>
            <div>
                <h3>Car Details:</h3>
                <ul>
                    <li>Brand: <?= $booked_car['brand'] ?></li>
                    <li>Model: <?= $booked_car['model'] ?></li>
                    <li>Year: <?= $booked_car['year'] ?></li>
                    <li>Transmission: <?= $booked_car['transmission'] ?></li>
                    <li>Fuel Type: <?= $booked_car['fuel_type'] ?></li>
                    <li>Passengers: <?= $booked_car['passengers'] ?></li>
                    <li>Price: <?= $booked_car['daily_price_huf'] ?> HUF/day</li>

                </ul>
            </div>


            <div>
                <h3>Booking Period:</h3>
                <p>From: <?= $booking['rent_start'] ?></p>
                <p>To: <?= $booking['rent_end'] ?></p>
            </div>

            <div>
                <h3>User Details:</h3>
                <p>Name: <?= $_SESSION['user']['full_name'] ?></p>
                <p>Email: <?= $user_email ?></p>
            </div>


        </div>
    <?php else: ?>
        <p>Booking details not found.</p> <!-- Handle cases where booking or car details are missing -->
    <?php endif; ?>
    <div class="mt-4">
        <a href="<?= $_SESSION['user']['admin_status'] ? "admin_profile.php" : "user_profile.php"; ?>" class="btn btn-primary">Back to Profile</a>
    </div>

</body>

</html>