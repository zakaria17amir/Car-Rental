<?php
session_start();
include_once('storage.php');
$cars_storage = new JsonIO('cars.json');
$existing_cars = $cars_storage->load();

session_start();

$success = false;
$success_message = null;
$input = $_POST;
//print_r($input);

function validate_car($input)
{
    $errors = [];
    if (!isset($input["brand"])) {
        $errors["brand"] = "Brand is required";
    } else if ($input["brand"] == "" || strlen($input["brand"]) < 2) {
        $errors["brand"] = "Brand must be at least 2 characters long";
    }

    if (!isset($input["model"])) {
        $errors["model"] = "Model is required";
    } else if ($input["model"] == "" || strlen($input["model"]) < 2) {
        $errors["model"] = "Model must be at least 2 characters long";
    }

    if (!isset($input["year"])) {
        $errors["year"] = "Year is required";
    } else if ($input["year"] < 2000 || $input["year"] > 2024) {
        $errors["year"] = "Year must be between 2000 and 2024";
    }

    if (!isset($input["transmission"])) {
        $errors["transmission"] = "Transmission is required";
    }



    if (!isset($input["passengers"])) {
        $errors["passengers"] = "Number of passengers is required";
    } else if ($input["passengers"] < 1 || $input["passengers"] > 8) {
        $errors["passengers"] = "Number of passengers must be between 1 and 8";
    }

    if (!isset($input["daily_price_huf"])) {
        $errors["daily_price_huf"] = "Daily price is required";
    } else if ($input["daily_price_huf"] < 5000 || $input["daily_price_huf"] > 100000) {
        $errors["daily_price_huf"] = "Daily price must be between 5000 and 100000";
    }

    if (!isset($input["image"])) {
        $errors["image"] = "Image is required";
    } else if (!filter_var($input["image"], FILTER_VALIDATE_URL)) {
        $errors["image"] = "Invalid image URL";
    } else {
        $fileExtension = strtolower(pathinfo($input["image"], PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        if (!in_array($fileExtension, $imageExtensions)) {
            $errors["image"] = "Invalid image extension";
        }
    }

    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = validate_car($input);

    if (empty($errors)) {
        $new_car['id'] = sizeof($existing_cars) + 1;
        $new_car['brand'] = $_POST['brand'];
        $new_car['model'] = $_POST['model'];
        $new_car['year'] = $_POST['year'];
        $new_car['transmission'] = $_POST['transmission'];
        $new_car['fuel_type'] = $_POST['fuel_type'];
        $new_car['passengers'] = $_POST['passengers'];
        $new_car['daily_price_huf'] = $_POST['daily_price_huf'];
        $new_car['image'] = $_POST['image'];
        $new_car['is_rented'] = false;
        $new_car['rent_start'] = null;
        $new_car['rent_end'] = null;

        $existing_cars[] = $new_car;
        $cars_storage->save($existing_cars);
        $success = true;
        $success_message = "Car added";
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


    <div class="bg-gray-700 flex justify-center items-center min-h-screen">
        <div class="card font-semibold w-full text-black max-w-xl bg-blue-400  p-6">
            <h2 class="text-center text-2xl font-semibold mb-4">Car Information</h2>
            <form action="add_car.php" method="post">

                <!-- Brand Field -->
                <div>
                    <label for="brand" class="block text-white">Car Brand</label>
                    <input type="text" id="brand" name="brand" placeholder="Enter car brand" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
                </div>

                <!-- Model Field -->
                <div>
                    <label for="model" class="block text-white">Car Model</label>
                    <input type="text" id="model" name="model" placeholder="Enter car model" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
                </div>

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
                <div>
                    <button type="submit" class="bg-green btn btn-accent w-full">Submit</button>
                </div>

            </form>
            <?php
            if (!empty($success))    echo "<p class='text-yellow-500'> . $success_message ";
            if (!empty($errors)) { // Display error messages here
                foreach ($errors as $error) {
                    echo "<p class='text-red-500'>" . $error . "</p>";
                }
            }
            ?>
        </div>
    </div>
</body>

</html>



