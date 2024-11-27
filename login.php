<?php
	session_start(); // 세션 시작

	// 로그인 성공 여부와 메시지 초기화
	$login_success = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : false;
	$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
	unset($_SESSION['message']); // 메시지 출력 후 세션에서 제거

	// 로그인 상태 확인
	$is_logged_in = isset($_SESSION['username']);
?>


<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login_style.css"> <!-- CSS 파일 연동 -->


    <script>
        function showAlert(message) {
            alert(message); // 로그인 실패 시 경고 메시지 표시
        }
    </script>

    <style>
        /* 버튼 사이 간격을 없애기 위한 스타일 */
        form {
            margin: 0; /* 폼의 기본 여백 제거 */
            padding: 0; /* 폼의 기본 패딩 제거 */
        }

        input[type="submit"] {
            margin: 1; /* 버튼 간격 제거 */
            display: block; /* 블록 요소로 설정하여 줄바꿈 */
            width: device-width /* 버튼 너비 */
        }
    </style>


</head>
<body>
	<div class="container"> <!-- 제목과 폼을 감싸는 컨테이너 -->

		<!-- 왼쪽 박스 시작 -->
		<div class="left-side"> 
			<div class="title-box"> <!-- 제목 박스 -->
	    		<h1>SIGN IN</h1> 
	    		<p>Membership Only!</p>
	    	</div> <!-- 제목 박스 -->

	    	<div class="image-box"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTuMnkE3772W9MGxqe-w0e5VMszC0xdK5bC1w&s" alt="Placeholder Image"></div> 

	    	<!-- 로그인 상태에 따른 내용 출력 -->
	    	<?php if ($is_logged_in): ?>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p> <!-- 성공 메시지 출력 -->
                <form method="POST" action="logout.php"> <!-- 로그아웃 처리 -->
                    <input type="submit" value="LOGOUT" />
                </form>
            <?php else: ?>
                <form method="POST" action="login_proc.php">
                    <input type="text" name="id" placeholder="ID" required /> <!-- name 속성 추가 -->
                    <input type="password" name="pass" placeholder="Password" required /> <!-- name 속성 추가 -->
                    <input type="submit" value="LOGIN" />
                </form>
                <form method="GET" action="register.php"> <!-- 회원가입 처리 -->
                    <input type="submit" value="SIGN UP" /> <!-- SIGN UP 버튼 추가 -->
                </form>
            <?php endif; ?>

	    	<p class="copy">@tsh05</p> <!-- 새로운 copy 텍스트 추가 -->
	    </div> 
	    <!-- 왼쪽 박스 끝 -->

	    <div class="right-side"> <!-- 오른쪽 박스 --> </div> 
	</div> <!-- 컨테이너 끝 -->

	<!-- 사용자 로그인하지 않았는데 메세지 존재하는 경우 -->
	<?php if (!$is_logged_in && $message): ?>
        <script>
            showAlert("<?php echo addslashes($message); ?>"); // 실패 메시지 팝업 표시
        </script>
    <?php endif; ?>

</body>
</html>
