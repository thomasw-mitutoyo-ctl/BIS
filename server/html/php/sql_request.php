<?php

	function editAppointmentDB($db, $termin,$id)
	{
		if($db == null) throw new Exception("Database is not given");

		$stmt = $db->prepare("UPDATE events SET title =?, date =?, time =?,  descr =?, startdate =?, enddate  =? WHERE id =?");
		$stmt->bind_param('sssssss', 
			$termin['title'], 
			$termin['date'], 
			$termin['time'], 
			$termin['description'], 
			$termin['showStart'], 
			$termin['enddate'],
			$id);

		try{
			$stmt->execute();
		}
		catch(Exception $ex){};
	}
	
	function newAppoinmentDB($db, $termin)
	{
		if($db == null) throw new Exception("Database is not given");

		$stmt = $db->prepare("INSERT INTO `events` (`title`, `date`, `time`, `descr`, `startdate`, `enddate`) VALUES (?,?,?,?,?,?)");
		$stmt->bind_param('ssssss', 
			$termin['title'], 
			$termin['date'], 
			$termin['time'], 
			$termin['description'], 
			$termin['showStart'], 
			$termin['enddate']);

		$stmt->execute();
	}
	
	function deleteAppoinmentDB($db, $id)
	{
		if($db == null) throw new Exception("Database is not given");
		
		$stmt = $db->prepare("DELETE FROM `events` WHERE `id` = ?");
		$stmt->bind_param('s', $id);
		$stmt->execute();

		try{
			$stmt->execute();
		}
		catch(Exception $ex){};
	}
	
	function selectRelevantAppointmentsForDate($db, $date)
	{
		if($db == null) return null;
		
		$date = date("Y-m-d", $date);
		$now = date("H:i:s");
		
		$stmt = $db->prepare(
			"SELECT * FROM events 
			WHERE (?= date AND ? <= time) 
			OR (? BETWEEN startdate AND date AND ? != date) 
			OR (? BETWEEN date AND enddate) ORDER BY date,time");
		
		$stmt->bind_param('sssss', $date, $now, $date, $date, $date);
		$stmt->execute();
		
		return $stmt->get_result();
	}
	
	function selectAppointmentsForDate($db, $date)
	{
		if($db == null) return null;

		$date = date("Y-m-d", $date);
		$now = date("H:i:s");
		
		$stmt = $db->prepare(
			"SELECT * FROM events 
			WHERE ?= date ORDER BY date,time");
		
		$stmt->bind_param('s', $date);
		$stmt->execute();
		
		return $stmt->get_result();
	}

	function selectAllAppointments($db){
		if($db == null) return null;

		$stmt = $db->prepare(
			"SELECT * FROM events ORDER BY date,time");
		
		$stmt->execute();
		
		return $stmt->get_result();
	}
	
	function selectAppointmentsByID($db, $id)
	{
		if($db == null) return null;

		$stmt = $db->prepare("SELECT title,date,time,descr,startdate,enddate,id FROM events WHERE id = ?");
		$stmt->bind_param('s', $id);
		$stmt->execute();
		
		return $stmt->get_result();
	}

	function selectBirthdays($db, $date){
		if($db == null) return null;

		$day = date('d', $date);
		$month = date('m', $date);

		$stmt = $db->prepare("SELECT `key`,`date`,`name` FROM birthdays WHERE (month(date) =? and (day(date) =?))");
		$stmt->bind_param('ss', $month, $day);
		$stmt->execute();
		
		return $stmt->get_result();
	}

	function selectAllBirthdaysDb($db){
		if($db == null) return null;

		$stmt = $db->prepare("SELECT `key`,`date`,`name` FROM `birthdays` ORDER BY `name`;");
		$stmt->execute();
		
		return $stmt->get_result();
	}
	
	function selectMessages($db)
	{
		if($db == null) return null;

		$stmt = $db->prepare("SELECT id,startdate,enddate,message FROM tickermsg ORDER BY startdate");
		$stmt->execute();
		
		return $stmt->get_result();
	}
	function selectMessagesForDate($db, $date)
	{
		if($db == null) return null;

		$date = date("Y-m-d", $date);
		$stmt = $db->prepare("SELECT id,startdate,enddate,message FROM tickermsg WHERE ? BETWEEN startdate AND enddate;");
		$stmt->bind_param('s', $date);
		$stmt->execute();
		
		return $stmt->get_result();
	}
	
	function deleteMessageDB($db, $id)
	{
		if($db == null) throw new Exception("Database is not given");

		$stmt = $db->prepare("DELETE FROM tickermsg WHERE `id` = ?");
		$stmt->bind_param('s', $id);
		$stmt->execute();

		try{
			$stmt->execute();
		}
		catch(Exception $ex){ }
	}
	
	function deleteBirthdayDB($db, $id)
	{
		if($db == null) throw new Exception("Database is not given");

		$stmt = $db->prepare("DELETE FROM birthdays WHERE `key` = ?");
		$stmt->bind_param('s', $id);
		$stmt->execute();

		try{
			$stmt->execute();
		}
		catch(Exception $ex){ }
	}
	
	function newMessageDB($db, $event)
	{
		if($db == null) throw new Exception("Database is not given");

		$stmt = $db->prepare("INSERT INTO tickermsg (`startdate`, `enddate`, `message`) VALUES (?, ?, ?)");
		$stmt->bind_param('sss', $event['startdate'], $event['enddate'], $event['message']);
		$stmt->execute();
	}

	
	function newBirthdayDB($db, $birthday)
	{
		if($db == null) throw new Exception("Database is not given");

		$stmt = $db->prepare("INSERT INTO birthdays (`name`, `date`) VALUES (?, ?)");
		$stmt->bind_param('ss', $birthday['name'], $birthday['date']);
		$stmt->execute();
	}
	
	function cleanMessages($db){
		if($db == null) throw new Exception("Database is not given");

		$db->query("DELETE FROM `bis_test`.`tickermsg` WHERE enddate < DATE_ADD(now(), INTERVAL -2 DAY)");
	}
	
	function cleanAppointments($db){
		if($db == null) throw new Exception("Database is not given");

		$db->query("DELETE FROM `bis_test`.`events` WHERE enddate < DATE_ADD(now(), INTERVAL -2 DAY)");
	}

	function cleanDatabase($db){
		if($db == null) throw new Exception("Database is not given");

		cleanMessages($db);
		cleanAppointments($db);
	}

	function enumerateResult($result){
		if($result == null) return [];

		$rows = [];
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		return $rows;
	}

	/*
	Connects to the database using the given parameters
	*/
	function connectDatabase($server, $username, $password, $database){
		$db = new MySQLi($server, $username, $password, $database);
		if(!$db->connect_errno){
			$db->query("SET NAMES 'utf8';");
			cleanDatabase($db);
			return $db;
		}
		else{
			return null;
		}
	}
	
	/*
    Executes the needed sql statements to create the tables in the database
    */
    function setupDatabase($db){
		if($db == null) throw new Exception("Database is not given");
		
        $sqlBirthdays = "
        CREATE TABLE IF NOT EXISTS `birthdays` (
            `key` int(11) NOT NULL AUTO_INCREMENT,
            `date` date NOT NULL,
            `name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`key`)
		);";
		
		$sqlEvents ="
		CREATE TABLE IF NOT EXISTS `events` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` text NOT NULL,
            `date` date NOT NULL,
            `time` time NOT NULL,
            `descr` mediumtext CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
            `startdate` date NOT NULL,
            `enddate` date NOT NULL,
            PRIMARY KEY (`id`)
        );";

        $sqlTicker = "
		CREATE TABLE IF NOT EXISTS `tickermsg` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `startdate` date NOT NULL,
            `enddate` date NOT NULL,
            `message` text NOT NULL,
            PRIMARY KEY (`id`)
        );";

        // Execute the create querys
        $db->query($sqlBirthdays);
        $db->query($sqlEvents);
        $db->query($sqlTicker);
    }
?>