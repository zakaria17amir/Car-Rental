<?php
session_start();
include_once('storage.php');
$users_storage = new JsonIO('users.json');
$existing_users = $users_storage->load();
$success = false;

function validate_registration($input)
{
    global $existing_users;
    $errors = [];
    if (!isset($input["full_name"]) || empty(trim($input['full_name']))) {
        $errors["full_name"] = "Full name is required";
    } else if ($input["full_name"] == "" || strlen($input["full_name"]) < 2) {
        $errors["full_name"] = "Full name must be at least 2 characters long";
    }

    if (!isset($input["email"])) {
        $errors["email"] = "Email is required";
    } else if (!filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email";
    } else {
        foreach ($existing_users as $user) {
            if (isset($user['email']) && $user['email'] == $input["email"]) {
                $errors["email"] = "Email already exists. Please use a different email";
                break;
            }
        }
    }

    if (!isset($input["password"])) {
        $errors["password"] = "Password is required";
    } else if ($input["password"] == "" || strlen($input["password"]) < 8) {
        $errors["password"] = "Password must be at least 8 characters long";
    }

    if (!isset($input["confirm_password"])) {
        $errors["confirm_password"] = "Confirm password is required";
    } else if ($input["confirm_password"] != $input["password"]) {
        $errors["confirm_password"] = "Passwords do not match";
    }
    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = validate_registration($_POST);

    if (empty($errors)) {
        $new_user = [];
        $new_user['full_name'] = $_POST['full_name'];
        $new_user['email'] = $_POST['email'];
        $new_user['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $new_user['admin_status'] = false;
        $existing_users[] = $new_user;
        $users_storage->save($existing_users);
        $success = true;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body >
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
    <div class="bg-gray-700 flex justify-center items-center min-h-screen">
    <div class="card font-semibold w-full text-black max-w-xl bg-blue-400  p-6">
        <h2 class="text-center text-2xl font-semibold mb-4">Registration</h2>
        <form action="registration.php" method="post">

            <!-- Name Field -->
            <div>
                <label for="full_name" class="block text-white">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-white">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
            </div>

            <div>
                <label for="password" class="block text-white">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
            </div>

            <div>
                <label for="confirm_password" class="block text-white">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" class="input input-bordered w-full text-white bg-gray-800 placeholder-gray-400" />
            </div>


            <!-- Submit Button -->
            <div>
                <button type="submit" class="bg-green btn btn-accent w-full">Register</button>
            </div>

        </form>
        <?php

        if (!empty($errors)) { // Display error messages here
            foreach ($errors as $error) {
                echo "<p class='text-red-500'>" . $error . "</p>";
            }
        }
        ?>
        <?php if ($success): ?>
            <div role="alert" class="alert alert-success">
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
                <span>Registration Successful!</span>
            </div>
        <?php endif; ?>
    </div></div>
</body>

</html>