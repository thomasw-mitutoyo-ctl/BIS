<?php
/*

This file provides proxy functions to access the database and insert/query data as needed by the view

*/

/*
Returns all birthdays and all ticker messages for the current date
*/
function getAllTickerMessages($db, $today){
    $all = [];

    // Get birthdays and messages
    $birthdays = getBirthdays($db, $today);
    $messages = getMessages($db, $today);

    // Add missed birthdays from the weekend
    if(date('D', $today) === 'Mon') {
        $belatedBirthdays = getBirthdays($db, $today - 24 * 3600);

        foreach($belatedBirthdays as $b) {
            $all[] = $b." had a birthday yesterday";
        }

        $belatedBirthdays = getBirthdays($db, $today - 24 * 3600 * 2);
        
        foreach($belatedBirthdays as $b) {
            $all[] = $b." had a birthday two days ago";
        }
    }
    


    // Join the two collections
    foreach($birthdays as $b) {
        $all[] = "Happy Birthday " . $b;
    }

    foreach($messages as $m) {
        $all[] = $m;
    }

    return $all;
}

/*
Returns all appointments relevant to the current date
*/
function getRelevantAppointments($db, $today){
    $rows = enumerateResult(selectRelevantAppointmentsForDate($db, $today));

    $appointments = [];

    $dayNames = array(
        'So',
        'Mo', 
        'Di', 
        'Mi', 
        'Do', 
        'Fr', 
        'Sa', 
     );

    foreach($rows as $row) {
        $appointment["day"] = $dayNames[date("w", strtotime($row["date"]))];
        $appointment["date"] = $row["date"];
        $appointment["start"] = $row["startdate"];
        $appointment["end"] = $row["enddate"];
        $appointment["location"] = $row["descr"];
        $appointment["title"] = $row["title"];
        $appointment["time"] = date("H:i", strtotime($row["time"]));
        $appointments[] = $appointment;
    }

    return $appointments;
}

/**
 * Returns all appointments for the given date
 */
function getAppointments($db, $today){
    $rows = enumerateResult(selectAppointmentsForDate($db, $today));

    $appointments = [];

    foreach($rows as $row) {
        $appointment["id"] = $row["id"];
        $appointment["date"] = $row["date"];
        $appointment["start"] = $row["startdate"];
        $appointment["end"] = $row["enddate"];
        $appointment["location"] = $row["descr"];
        $appointment["title"] = $row["title"];
        $appointment["time"] = date("H:i", strtotime($row["time"]));

        $appointments[] = $appointment;
    }

    return $appointments;
}

function getAllAppointments($db){
    $rows = enumerateResult(selectAllAppointments($db));

    $appointments = [];

    foreach($rows as $row) {
        $appointment["id"] = $row["id"];
        $appointment["date"] = $row["date"];
        $appointment["start"] = $row["startdate"];
        $appointment["end"] = $row["enddate"];
        $appointment["location"] = $row["descr"];
        $appointment["title"] = $row["title"];
        $appointment["time"] = date("H:i", strtotime($row["time"]));

        $appointments[] = $appointment;
    }

    return $appointments;
}

/**
 * Returns the first appointment from the database which has the
 * given id
 */
function getAppointmentById($db, $id){
    $rows = enumerateResult(selectAppointmentsByID($db, $id));

    foreach($rows as $row) {
        $appointment["id"] = $row["id"];
        $appointment["date"] = $row["date"];
        $appointment["start"] = $row["startdate"];
        $appointment["end"] = $row["enddate"];
        $appointment["location"] = $row["descr"];
        $appointment["title"] = $row["title"];
        $appointment["time"] = date("H:i", strtotime($row["time"]));

        return $appointment;
    }

    return null;
}

/**
 * Returns the appointments from the database which are in the given month
 */
function getAppointmentsForMonth($db, $today){

    $month = date('m', $today);
    $year = date('y', $today);

    // Get all days in this month
    $list=array();
    for($d=1; $d<=31; $d++)
    {
        $time=mktime(12, 0, 0, $month, $d, $year);
        if (date('m', $time)==$month)       
            $list[] = $time;
    }

    $appointments = array();
    foreach ($list as $day) {
        $appointmentsForDay = getAppointments($db, $day);

        foreach ($appointmentsForDay as $a) {
            $appointments[] = $a;
        }
    }

    return $appointments;
}

/**
 * Returns all messages for the given date
 */
function getMessages($db, $today){
    $rows = enumerateResult(selectMessagesForDate($db, $today));

    $messages = [];

    foreach($rows as $row) {
        $m["message"] = $row["message"];
        $m["start"] = $row["startdate"];
        $m["end"] = $row["enddate"];
        $m["id"] = $row["id"];
        $messages[] = $m;
    }

    return $messages;
}

function getAllMessages($db){
    $rows = enumerateResult(selectMessages($db));

    $messages = array();

    foreach($rows as $row) {
        $m["message"] = $row["message"];
        $m["start"] = $row["startdate"];
        $m["end"] = $row["enddate"];
        $m["id"] = $row["id"];
        $messages[] = $m;
    }

    return $messages;
}

/**
 * Returns all birthdays for the given date
 */
function getBirthdays($db, $today){
    $rows = enumerateResult(selectBirthdays($db, $today));

    $birthdays = [];

    foreach($rows as $row) {
        $day["name"] = $row["name"];
        $day["date"] = $row["date"];
        $day["id"] = $row["key"];

        $birthdays[] = $day;
    }  

    return $birthdays;
}

/**
 * Returns all birthdays for the given date
 */
function getAllBirthdays($db){
    $rows = enumerateResult(selectAllBirthdaysDb($db));

    $birthdays = [];

    foreach($rows as $row) {
        $day["name"] = $row["name"];
        $day["date"] = $row["date"];
        $day["id"] = $row["key"];

        $birthdays[] = $day;
    }  

    return $birthdays;
}

/**
 * Adds a new appointment to the database
 */
function addNewAppointment($db, $title, $description, $start, $end, $date, $time){
    $appointment['title'] = $title;
    $appointment['date'] = $date;
    $appointment['time'] = $time;
    $appointment['description'] = $description;
    $appointment['showStart'] = $start;
    $appointment['enddate'] = $end;

    newAppoinmentDB($db, $appointment);
}

/**
 * Edits an existing appointment in the database
 */
function editExistingAppointment($db, $id, $title, $description, $start, $end, $date, $time){
    $appointment['title'] = $title;
    $appointment['date'] = $date;
    $appointment['time'] = $time;
    $appointment['description'] = $description;
    $appointment['showStart'] = $start;
    $appointment['enddate'] = $end;

    editAppointmentDB($db, $appointment, $id);
}

/**
 * Deletes an existing appointment
 */
function deleteExistingAppointment($db, $id){
    deleteAppoinmentDB($db, $id);
}

/**
 * Adds a new ticker message to the database
 */
function addNewMessage($db, $message, $start, $end){
    $m['message'] = $message;
    $m['startdate'] = $start;
    $m['enddate'] = $end;

    newMessageDB($db, $m);
}

/**
 * Adds a new birthday to the database
 */
function addNewBirthday($db, $name, $date){
    $m['name'] = $name;
    $m['date'] = $date;

    newBirthdayDB($db, $m);
}

/**
 * Deletes an existing ticker message
 */
function deleteExistingMessage($db, $id){
    deleteMessageDB($db, $id);
}


/**
 * Deletes an existing birthday
 */
function deleteExistingBirthday($db, $id){
    deleteBirthdayDB($db, $id);
}

?>
