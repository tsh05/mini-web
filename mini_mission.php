<?php
	// MAMP
	$host = "localhost";
	$user = "root";	
	$pw = "root";
	$db = "test_db_01";

	$db_conn = mysqli_connect($host, $user, $pw, $db);

	/*
	if ($db_conn) {
		echo "DB Connect OK";
	} else {
		echo "DB Connect Failed";
	}
	*/

	$name = isset($_GET['name']) ? $_GET['name'] : ''; 	// isset(): 변수에 값이 존재하는지 확인, 파라미터가 없으면 빈 문자열 반환

    $sql = "SELECT name, score FROM test_table_01 WHERE name = '$name'"; // query

    $result = mysqli_query($db_conn, $sql); 
    $row = mysqli_fetch_array($result);
    
    
    if ($row) {
    	echo "<div style='font-size: 32px; text-align: center; margin-top: 50px;'>";
        echo "{$row['name']} 학생의 성적은 {$row['score']}점 입니다";
    } else {
    	echo "<div style='font-size: 32px; text-align: center; margin-top: 50px;'>";
        echo "해당 학생을 찾을 수 없습니다";
    }  
?>