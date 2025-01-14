<?php
session_start();
include_once('storage.php');
$cars_storage = new JsonIO('cars.json');
$cars = $cars_storage->load();
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
    <h1>Home Page</h1>
    <p><a href="add_car.php">Add Car"></a></p>
    <p><a href="login.php">Login</a></p>
    <p><a href="registration.php">Register</a></p>
    <p><a href="profile.php">Profile"></a></p>
    <form action="" method="get" class="mb-4"> <!-- Form now uses GET -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="seats" class="block">Seats:</label>
                <input type="number" name="seats" id="seats" class="input input-bordered w-full" min="1" value="<?php echo isset($_GET['seats']) ? $_GET['seats'] : ''; ?>">
            </div>
            <div>
                <label class="block text-white">Transmission</label>
                <label class="inline-flex items-center text-white">
                    <input type="radio" name="transmission" value="Manual" class="radio text-white bg-gray-800" />
                    <span class="ml-2">Manual</span>
                </label>
                <label class="inline-flex items-center text-white">
                    <input type="radio" name="transmission" value="Automatic" class="radio text-white bg-gray-800" />
                    <span class="ml-2">Automatic</span>
                </label>
            </div>
            <div>
                <label for="start_date" class="block">Start Date:</label>
                <input type="date" name="start_date" id="start_date" class="input input-bordered w-full" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
            </div>
            <div>
                <label for="end_date" class="block">End Date:</label>
                <input type="date" name="end_date" id="end_date" class="input input-bordered w-full" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
            </div>
            <div>
                <label for="min_price" class="block">Min Price:</label>
                <input type="number" name="min_price" id="min_price" class="input input-bordered w-full" min="0" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">
            </div>
            <div>
                <label for="max_price" class="block">Max Price:</label>
                <input type="number" name="max_price" id="max_price" class="input input-bordered w-full" min="0" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Filter</button>
    </form>
    <div class="grid grid-cols-3 gap-4">
        <?php
        $filtered_cars = $cars; // Start with all cars

        // Apply filters if present in $_GET
        if (!empty($_GET)) {
            $filtered_cars = array_filter($cars, function ($car) {
                return (
                    (empty($_GET['seats']) || $car['passengers'] == $_GET['seats']) &&
                    (empty($_GET['transmission']) || $car['transmission'] == $_GET['transmission']) &&
                    (empty($_GET['start_date']) || ($car['is_rented'] && $car['rent_end'] <= $_GET['start_date']) || (!$car['is_rented'])) &&
                    (empty($_GET['end_date']) || (!$car['is_rented'] || $car['rent_start'] >= $_GET['end_date'])) &&
                    (empty($_GET['min_price']) || $car['daily_price_huf'] >= $_GET['min_price']) &&
                    (empty($_GET['max_price']) || $car['daily_price_huf'] <= $_GET['max_price'])

                );
            });
        }

        foreach ($filtered_cars as $car) : ?>
            <a href="car_details.php?id=<?= $car['id'] ?>">
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
                            <button class="btn btn-primary">Rent Now</button>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</body>

</html>