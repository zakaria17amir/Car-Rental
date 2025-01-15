<?php
session_start();
include_once('storage.php');
$users_storage = new JsonIO('users.json');
$users = $users_storage->load();
$cars_storage = new JsonIO('cars.json');
$cars = $cars_storage->load();
$bookings_storage = new JsonIO('bookings.json');
$bookings = $bookings_storage->load();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
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
                        <li><a class="bg-yellow-300" href="add_car.php">Add Car</a></li>
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

    <div class="card bg-base-100 w-96 h-96 shadow-xl">
        <figure>
            <img
                src="https://img.freepik.com/free-vector/blue-circle-with-white-user_78370-4707.jpg"
                alt="Profile Picture" />
        </figure>
        <div class="card-body">
            <h2 class="card-title">
                <?php echo $_SESSION['user']['full_name']; ?>
            </h2>
            <p><?php echo $_SESSION['user']['email']; ?></p>
        </div>
    </div>
    <div>
        <h2 class="text-4xl font-bold mb-4 text-center text-yellow-300">Bookings</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <?php foreach ($bookings as $index => $booking): ?>
            <?php
            $booked_car = null;
            foreach ($cars as $car_index => $car) { // Added $car_index for updating cars array
                if ($car['id'] == $booking['car_id']) {
                    $booked_car = $car;
                    $car_array_index = $car_index; // Save the index in $cars array
                    break;
                }
            }
            if ($booked_car):
            ?>
                <div class="card w-96 bg-base-100 shadow-xl">
                    <figure><img src="<?= $booked_car['image'] ?>" alt="Car" /></figure>
                    <div class="card-body">
                        <h2 class="card-title"><?= $booked_car['brand'] . ' ' . $booked_car['model'] ?></h2>
                        <p>Seats: <?= $booked_car['passengers'] ?></p>
                        <p>Transmission: <?= $booked_car['transmission'] ?></p>
                        <p class="text-xl ">
                            Booking Period: <?= $booking['rent_start'] ?> to <?= $booking['rent_end'] ?>
                        </p>
                        <div class="card-actions justify-end">
                            <form action="delete_booking.php" method="post">
                                <input type="hidden" name="delete_booking" value="<?= $index ?>">
                                <input type="hidden" name="car_index" value="<?= $car_array_index ?>">
                                <button type="submit" class="btn btn-error">Delete booking</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>


    <div>
        <h2 class="text-4xl font-bold mb-4 text-center text-green-400"><br><br><br>
            Cars</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <?php foreach ($cars as $car_index => $car) : ?>

            <div class="card card-compact bg-base-100 w-96 shadow-xl">
                <figure><img src="<?= $car['image'] ?>" alt="<?= $car['brand'] . ' ' . $car['model'] ?>" /></figure>
                <div class="card-body">
                    <h2 class="card-title"><?= $car['brand'] . ' ' . $car['model'] ?></h2>
                    <p>
                        Seats: <?= $car['passengers'] ?><br>
                        Transmission: <?= $car['transmission'] ?><br>
                        Price: <span class="text-xl font-bold"><?= $car['daily_price_huf'] ?> HUF/day</span>
                    </p>
                    <div class="card-actions justify-end">
                        <form action="edit_car.php" method="get">
                            <input type="hidden" name="edit_car" value="<?=$car_index?>">
                            <button type="submit" class="btn btn-primary">Edit Car</button>
                        </form>
                        <form action="delete_car.php" method="post">
                            <input type="hidden" name="delete_car" value="<?= $car_index ?>">
                            <button type="submit" class="btn btn-error">Delete Car</button>
                        </form>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

</body>

</html>