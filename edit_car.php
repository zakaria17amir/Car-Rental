<?php
session_start();
include_once('storage.php');

$cars_storage = new JsonIO('cars.json');
$cars = $cars_storage->load();

$car_index = null;
$errors = [];
$car = null;
$car_id = $_GET['edit_car'];

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['edit_car'])) {
    $car_index = $_GET['edit_car'];
    if (isset($cars[$car_index])) {
        $car = $cars[$car_index];
    } else {
        print_r($_GET);
        die("Car not found.");
    }
}


function validate_car($input)
{
    $errors = [];
    if (empty($input["brand"]) || strlen($input["brand"]) < 2) {
        $errors["brand"] = "Brand must be at least 2 characters long";
    }

    if (empty($input["model"]) || strlen($input["model"]) < 2) {
        $errors["model"] = "Model must be at least 2 characters long";
    }

    if (!isset($input["year"]) || $input["year"] < 2000 || $input["year"] > 2024) {
        $errors["year"] = "Year must be between 2000 and 2024";
    }

    if (!isset($input["passengers"]) || $input["passengers"] < 1 || $input["passengers"] > 8) {
        $errors["passengers"] = "Number of passengers must be between 1 and 8";
    }

    if (!isset($input["daily_price_huf"]) || $input["daily_price_huf"] < 5000 || $input["daily_price_huf"] > 100000) {
        $errors["daily_price_huf"] = "Daily price must be between 5000 and 100000";
    }

    if (empty($input["image"]) || !filter_var($input["image"], FILTER_VALIDATE_URL)) {
        $errors["image"] = "Invalid image URL";
    } else {
        $fileExtension = strtolower(pathinfo($input["image"], PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        if (!in_array($fileExtension, $validExtensions)) {
            $errors["image"] = "Invalid image extension";
        }
    }

    return $errors;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car_index = $_POST['car_index'];
    $car = $cars[$car_index] ?? null;

    if ($car) {
        $errors = validate_car($_POST);

        if (empty($errors)) {
            $cars[$car_index]['brand'] = $_POST['brand'] ?: $car['brand'];
            $cars[$car_index]['model'] = $_POST['model'] ?: $car['model'];
            $cars[$car_index]['year'] = $_POST['year'] ?: $car['year'];
            $cars[$car_index]['daily_price_huf'] = $_POST['daily_price_huf'] ?: $car['daily_price_huf'];
            $cars[$car_index]['image'] = $_POST['image'] ?: $car['image'];
            $cars[$car_index]['passengers'] = $_POST['passengers'] ?: $car['passengers'];
            $cars[$car_index]['transmission'] = $_POST['transmission'] ?: $car['transmission'];
            $cars[$car_index]['fuel_type'] = $_POST['fuel_type'] ?: $car['fuel_type'];

            $cars_storage->save($cars);
            header("Location: admin_profile.php");
            exit;
        }
    } else {
        print_r($_POST);
        die("Car not found.!!");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental Form</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header>
        <nav class="navbar bg-base-100">
            <div class="flex-1">
                <a class="btn btn-ghost text-xl bg-yellow-300 text-black" href="index.php">Car Rental</a>
            </div>
        </nav>
    </header>
    <div class="bg-gray-700 flex justify-center items-center min-h-screen">
        <div class="card font-semibold w-full text-black max-w-xl bg-blue-400  p-6">
            <h2 class="text-center text-2xl font-semibold mb-4">Edit Car Information</h2>
            <form action="edit_car.php" method="post">
                <input type="hidden" name="car_index" value="<?= $car_index ?>">

                <!-- Brand Field -->
                <label for="brand" class="block text-white">Car Brand</label>
                <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($car['brand'] ?? '') ?>" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />

                <!-- Model Field -->
                <label for="model" class="block text-white">Car Model</label>
                <input type="text" id="model" name="model" value="<?= htmlspecialchars($car['model'] ?? '') ?>" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
                <!-- Year Field -->
                <div>
                    <label for="year" class="block text-white">Car Year</label>
                    <input type="number" id="year" name="year" placeholder="Enter car year" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
                </div>

                <!-- Transmission Field (Radio Buttons) -->
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

                <!-- Fuel Type Field (Dropdown) -->
                <div>
                    <label for="fuel_type" class="block text-white">Fuel Type</label>
                    <select id="fuel_type" name="fuel_type" class="select select-bordered w-full text-white bg-gray-800 placeholder-gray-400">
                        <option disabled selected>Select fuel type</option>
                        <option value="petrol">Petrol</option>
                        <option value="diesel">Diesel</option>
                        <option value="electric">Electric</option>
                    </select>
                </div>

                <!-- Passengers Field -->
                <div>
                    <label for="passengers" class="block text-white">Number of Passengers</label>
                    <input type="number" name="passengers" id="passengers" placeholder="Enter number of passengers" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
                </div>

                <!-- Daily Price Field -->
                <div>
                    <label for="daily_price_huf" class="block text-white">Daily Price (HUF)</label>
                    <input type="number" name="daily_price_huf" id="daily_price_huf" placeholder="Enter daily price" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
                </div>

                <!-- Image Field -->
                <div>
                    <label for="image" class="block text-white">Car Image URL</label>
                    <input type="text" name="image" id="image" placeholder="Enter image URL" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
                </div>



                <!-- Submit Button -->
                <button type="submit" class="bg-green btn btn-accent w-full">Submit</button>
            </form>

            <!-- Error Display -->
            <?php if (!empty($errors)): ?>
                <div class="mt-4">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-red-500"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>