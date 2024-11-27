<?php
	session_start(); // 세션 시작
	session_destroy(); // 세션 종료
	header("Location: login.php"); // 로그인 페이지로 리다이렉트
	exit();
?>
