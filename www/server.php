<?
require_once "config.php";

$data = $_REQUEST;

if (isset($data['getZones'])) {
	
	$arr = array();
	$stmt = $db->prepare("SELECT * FROM zones ORDER BY title ASC");
	if ($stmt->execute()) { 
		$result = $stmt->get_result();
		$row_count = mysqli_num_rows( $result );		
		if( $row_count > 0 ) {
			while( $row = mysqli_fetch_array( $result ) ) {
				array_push( $arr , $row );
			}
		}
		echo json_encode( $arr );
	}

	$stmt->close();
}

if (isset($data['insertZone'])) {
	
	$arr = array();
	if ($data['insertZone'] == "pubrunda") {
		$arr = array();
		$stmt = $db->prepare("INSERT INTO zones (title, url) VALUES (?, ?)");
		$stmt->bind_param("ss", $data['title'], $data['url']);
		if ($stmt->execute()) { 
			array_push( $arr , 1 );
		}
		else {
			array_push( $arr , 0 );
		}
		$stmt->close();
	}
	else {
		array_push( $arr , 0 );
	}
	echo json_encode( $arr );
}

if (isset($data['deleteZone'])) {
	
	$arr = array();
	if ($data['deleteZone'] == "pubrunda") {
		$arr = array();
		$stmt = $db->prepare("DELETE FROM zones WHERE title = ?");
		$stmt->bind_param("s", $data['title']);
		if ($stmt->execute()) { 
			array_push( $arr , 1 );
		}
		else {
			array_push( $arr , 0 );
		}
		$stmt->close();
	}
	else {
		array_push( $arr , 0 );
	}
	echo json_encode( $arr );
}

if (isset($data['getIp'])) {
	$arr = array();
	array_push( $arr , $_SERVER['REMOTE_ADDR'] );
	
	
	$stmt = $db->prepare("SELECT * FROM users WHERE ip = ?");
	$stmt->bind_param("s",  $_SERVER['REMOTE_ADDR']);
	
	if ($stmt->execute()) { 
		$result = $stmt->get_result();
		$row_count = mysqli_num_rows( $result );		
		if( $row_count > 0 ) {
			$ustmt = $db->prepare("UPDATE users SET lastactive = now() WHERE ip = ?");
			$ustmt->bind_param("s",  $_SERVER['REMOTE_ADDR']);
			$ustmt->execute();
			$ustmt->close();
		}
		else {
			$istmt = $db->prepare("INSERT INTO users (ip, lastactive) VALUES (?, now())");
			$istmt->bind_param("s",  $_SERVER['REMOTE_ADDR']);
			$istmt->execute();
			$istmt->close();
		}
	}
	
	$stmt->close();
	
	$cstmt = $db->prepare("SELECT COUNT(*) FROM users WHERE lastactive > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
	if ($cstmt->execute()) { 
		$cresult = $cstmt->get_result();
		$crc = mysqli_num_rows( $cresult );		
		if( $crc > 0 ) {
			while( $crow = mysqli_fetch_array( $cresult ) ) {
				array_push( $arr , $crow[0] );
			}
		}
		else {
			array_push( $arr , 0 );
		}
	}
	$cstmt->close();
	
	echo json_encode( $arr );
}

?>