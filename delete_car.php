<?php
session_start();
include_once('storage.php');

$cars_storage = new JsonIO('cars.json');
$cars = $cars_storage->load();
$bookings_storage = new JsonIO('bookings.json');
$bookings = $bookings_storage->load();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_car'])) {
    $car_index = $_POST['delete_car'];
    $deleted_car_id = $cars[$car_index]['id'];

    $bookings = array_filter($bookings, function ($booking) use ($deleted_car_id) {
        return $booking['car_id'] != $deleted_car_id;
    });

    $bookings = array_values($bookings);
    $bookings_storage->save($bookings);

    unset($cars[$car_index]);
    $cars = array_values($cars);
    $cars_storage->save($cars);

    header("Location: admin_profile.php");
    exit;
} else {
    header("Location: admin_profile.php");
    exit;
}
