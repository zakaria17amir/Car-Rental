<?php
session_start();
include_once('storage.php');
$users_storage = new JsonIO('users.json');
$users = $users_storage->load();


function validate_login($input)
{
    global $users;
    $errors = [];
    if (!isset($input["email"]) || empty(trim($input["email"]))) {
        $errors["email"] = "Email is required";
    } elseif (!filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email";
    }

    if (!isset($input["password"]) || empty(trim($input["password"]))) {
        $errors["password"] = "Password is required";
    }

    if (empty($errors)) {
        foreach ($users as $user) {
            if ($user['email'] === $input['email']) {
                if (password_verify($input['password'], $user['password'])) {
                    $_SESSION['user'] = [
                        'email' => $user['email'],
                        'full_name' => $user['full_name'],
                        'admin_status' => $user['admin_status']
                    ];
                    return [];
                } else {
                    $errors["mismatch"] = "Invalid email or password";
                }
                break;
            }
        }
        if (!isset($errors["mismatch"])) {
            $errors["mismatch"] = "Invalid email or password";
        }
    }
    return $errors;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = validate_login($_POST);
    if (empty($errors) && isset($_SESSION['user'])) {
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
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
        <div class="hero-content flex-col lg:flex-row-reverse">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl font-bold">Login now!</h1>
                <p class="py-6">
                    Log in to book cars
                </p>
            </div>
            <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
                <form method="post" action="login.php" class="card-body">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" name="email" placeholder="email" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input type="password" name="password" placeholder="password" class="input input-bordered" />
                        <label class="label">
                            <a href="#" class="label-text-alt link link-hover">Forgot password?</a>
                            <a href="registration.php" class="label-text-alt link link-hover">Registration</a>
                        </label>
                    </div>
                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                    <?php
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            echo "<p class='text-red-500'>" . $error . "</p>";
                        }
                    }
                    ?>
                </form>
            </div>
        </div>

    </div>








</body>

</html>