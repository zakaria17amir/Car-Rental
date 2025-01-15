<?php
session_start();
include_once('storage.php');

$bookings_storage = new JsonIO('bookings.json');
$bookings = $bookings_storage->load();
$cars_storage = new JsonIO('cars.json');
$cars = $cars_storage->load();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_booking']) && isset($_POST['car_index'])) {

    $booking_index = $_POST['delete_booking'];
    $car_index = $_POST['car_index'];
    $deleted_car_id = $bookings[$booking_index]['car_id'];

    unset($bookings[$booking_index]);
    $bookings = array_values($bookings); 
    $bookings_storage->save($bookings);
    $other_bookings = array_filter($bookings, function ($b) use ($deleted_car_id) {
        return $b['car_id'] == $deleted_car_id;
    });

    if (!empty($other_bookings)) {
        usort($other_bookings, function ($a, $b) {
            return strtotime($a['rent_end']) - strtotime($b['rent_end']);
        });
        $latest_booking = end($other_bookings);
        $cars[$car_index]['is_rented'] = true;
        $cars[$car_index]['rent_start'] = $latest_booking['rent_start'];
        $cars[$car_index]['rent_end'] = $latest_booking['rent_end'];
        $cars_storage->save($cars);
    } else {
        $cars[$car_index]['is_rented'] = false;
        $cars[$car_index]['rent_start'] = null;
        $cars[$car_index]['rent_end'] = null;
        $cars_storage->save($cars);
    }


    header("Location: admin_profile.php");
    exit;
} else {
    
    header("Location: admin_profile.php");
    exit;
}

?>