<?php
session_start();
include_once('storage.php');
$cars_storage = new JsonIO('cars.json');
$cars = $cars_storage->load();
$bookings_storage = new JsonIO('bookings.json'); // Initialize bookings storage
$bookings = $bookings_storage->load() ?: [];

$car = null;
if (isset($_GET['id'])) {
    $car_id = $_GET['id'];


    foreach ($cars as $c) {
        if ($c['id'] == $car_id) {
            $car = $c;
            break;
        }
    }
} else {
    echo "<p>No car ID provided.</p>";
}

$min_booking_date = date('Y-m-d');
if ($car['is_rented']) {
    $rent_end_date = date('Y-m-d', strtotime($car['rent_end']));
    $min_booking_date = max($min_booking_date, $rent_end_date);
}
$max_booking_date = date('Y-m-d', strtotime($min_booking_date . ' + 30 days'));

$booking_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php?redirect_url=car_details.php?id=" . $car['id']); // Redirect to login with redirect URL
        exit;
    }

    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (strtotime($start_date) < strtotime($min_booking_date)) {

        $booking_error = "Booking start date cannot be earlier than " . $min_booking_date . ".";
    } elseif (strtotime($end_date) > strtotime($max_booking_date)) {
        $booking_error = "End date should be at most 30 days from today.";
    } elseif (strtotime($start_date) >= strtotime($end_date)) {
        $booking_error = "Start date must be before end date.";
    } else {
        // Check for overlapping bookings
        $overlapping_booking = false;
        foreach ($bookings as $booking) {
            if (
                $booking['car_id'] == $car_id &&
                strtotime($start_date) < strtotime($booking['rent_end']) &&
                strtotime($end_date) > strtotime($booking['rent_start'])
            ) {
                $overlapping_booking = true;
                break;
            }
        }

        if ($overlapping_booking) {
            $booking_error = "Selected dates overlap with existing booking.";
        } else {
            $new_booking = [
                'car_id' => $car_id,
                'user_email' => $_SESSION['user']['email'],
                'rent_start' => $start_date,
                'rent_end' => $end_date
            ];

            $bookings[] = $new_booking;
            $bookings_storage->save($bookings);

            $car_index = -1;
            foreach ($cars as $index => $c) {
                if ($c['id'] == $car_id) {
                    $car_index = $index;
                    break;
                }
            }

            if ($car_index !== -1) {
                $cars[$car_index]['is_rented'] = true;
                $cars[$car_index]['rent_start'] = $start_date;
                $cars[$car_index]['rent_end'] = $end_date;
                $cars_storage->save($cars);
            }
            header("Location: booking_success.php");
            exit;
        }
    }

    if ($booking_error) {
        header("Location: booking_fail.php?error=" . urlencode($booking_error));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Details</title>
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
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row ">
            <img src="<?= $car['image'] ?>" class="max-w-sm rounded-lg shadow-2xl" />
            <div>
                <h1 class="text-5xl font-bold"><?= $car['brand'] . ' ' . $car['model'] ?></h1>
                <div class="py-6 text-xl font-bold">
                    <p>Year: <?= $car['year'] ?></p>
                    <p>Transmission: <?= $car['transmission'] ?></p>
                    <p>Fuel Type: <?= $car['fuel_type'] ?></p>
                    <p>Passengers: <?= $car['passengers'] ?></p>
                    <p>Price: <?= $car['daily_price_huf'] ?> HUF/day</p>

                    <p><?php if ($car['is_rented']): ?>
                            Available after: <?= $car['rent_end'] ?>
                        <?php else: ?>
                            Available for booking
                        <?php endif; ?> </p>

                    <form action="car_details.php?id=<?= $car['id'] ?>" method="post">
                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                        <div class="mb-4">
                            <label for="start_date" class="block">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" class="input input-bordered w-full" min="<?= $min_booking_date ?>" max="<?= $max_booking_date ?>" value="<?= $min_booking_date ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="end_date" class="block">End Date:</label>
                            <input type="date" id="end_date" name="end_date" class="input input-bordered w-full" min="<?= $min_booking_date ?>" max="<?= $max_booking_date ?>" value="<?= $min_booking_date ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Book Now</button>
                        <?php if (isset($booking_error)) : ?>
                            <p class="text-red-500"><?= $booking_error ?></p>
                        <?php endif; ?>
                    </form>


                    <a href="index.php" class="btn btn-secondary">Back to Car List</a>

                </div>

            </div>
        </div>
    </div>
</body>

</html>