<?php
session_start(); // 세션 시작

$is_logged_in = isset($_SESSION['username']); // 로그인 상태 확인

$host = 'localhost';
$db = 'test_db';
$user = 'root';
$pass = 'root';

$db_conn = mysqli_connect($host, $user, $pass, $db);
if ($db_conn->connect_error) {
    die("Connection failed: " . $db_conn->connect_error);
}

$id = $name = $phone = $email = ""; // 입력 초기값, 중복값 체크 시 기존 입력값 날아가는 것 방지 위한 변수 선언

$error_messages = [
    'id' => "",
    'password' => "",
    'email' => "",
    'phone' => ""
];

function checkDuplicates($db_conn, $id, $email, $phone) {
    $messages = []; // 오류 메시지를 담을 배열 초기화

    // ID 중복 검사
    $stmt_id = $db_conn->prepare("SELECT * FROM user WHERE id = ?");
    $stmt_id->bind_param("s", $id);
    $stmt_id->execute();
    $result_id = $stmt_id->get_result();
    if ($result_id->num_rows > 0) {
        $messages['id'] = "That username is taken. Try another.";
    }

    // 이메일 중복 검사
    $stmt_email = $db_conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt_email->bind_param("s", $email);
    $stmt_email->execute();
    $result_email = $stmt_email->get_result();
    if ($result_email->num_rows > 0) {
        $messages['email'] = "Email is already in use.";
    }

    // 전화번호 중복 검사
    $stmt_phone = $db_conn->prepare("SELECT * FROM user WHERE phone = ?");
    $stmt_phone->bind_param("s", $phone);
    $stmt_phone->execute();
    $result_phone = $stmt_phone->get_result();
    if ($result_phone->num_rows > 0) {
        $messages['phone'] = "Phone number is already in use.";
    }
    return $messages;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = $_POST['pass'];
    $confirm_password = $_POST['pass_confirm'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // 중복 검사
    $duplicate_messages = checkDuplicates($db_conn, $id, $email, $phone);
    $error_messages['id'] = $duplicate_messages['id'] ?? "";
    $error_messages['email'] = $duplicate_messages['email'] ?? "";
    $error_messages['phone'] = $duplicate_messages['phone'] ?? "";

    // 비밀번호 확인
    if ($password !== $confirm_password) {
        $error_messages['password'] = "Those passwords didn’t match. Try again.";
    }

    // 모든 검증 통과 시 데이터베이스에 사용자 추가
    if (empty($error_messages['id']) && empty($error_messages['password']) && empty($error_messages['email']) && empty($error_messages['phone'])) {
        // 비밀번호를 SHA-256으로 해싱
        $hashedPassword = hash('sha256', $password);

        $query = $db_conn->prepare("INSERT INTO user (id, pw, username, phone, email) VALUES (?, ?, ?, ?, ?)");
        $query->bind_param("sssss", $id, $hashedPassword, $name, $phone, $email);
        
        if ($query->execute()) {
            // 회원가입 완료 후 로그인 페이지로 리다이렉트
            header("Location: login.php");
            exit();
        } else {
            $error_messages['id'] = "Failed to create an account. Please try again.";
        }
    }
}

$db_conn->close();
?>


<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="login_style.css"> <!-- CSS 파일 연동 -->
    <style>
        /* 버튼 스타일 통일 */
        .button-container input {
            width: 45%; /* 버튼 너비 설정 */
            padding: 10px; /* 내부 여백 설정 */
            margin: 10px; /* 여백 설정 */
            border: none; /* 경계선 제거 */
            border-radius: 5px; /* 모서리 둥글게 설정 */
            background-color: var(--lightblue); /* 버튼 색상 설정 */
            color: whitesmoke; /* 텍스트 색상 설정 */
            cursor: pointer; /* 포인터 커서 설정 */
            font-family: "Times New Roman", sans-serif; /* 폰트 설정 */
            font-size: 17px; /* 폰트 크기 설정 */
            box-shadow: -5px -5px 10px #fff, 5px 5px 10px #babebc; /* 그림자 효과 추가 */
        }
        .input-field {
        width: 100%; /* 너비를 100%로 설정 */
        padding: 10px; /* 내부 여백 설정 */
        margin: 10px 0; /* 상하 여백 설정 */
        border: 1px solid #ccc; /* 테두리 설정 */
        border-radius: 5px; /* 모서리 둥글게 설정 */
        font-size: 16px; /* 폰트 크기 설정 */
    </style>
</head>
<body>
    <div class="container"> <!-- 제목과 폼을 감싸는 컨테이너 -->
        
        <div class="left-side"> <!-- 왼쪽 박스 -->
            <h1>Create Account</h1> <!-- 제목을 중앙 정렬 -->
            <p>You don't have an account? Join our membership!</p> 

            <div class="image-box"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTuMnkE3772W9MGxqe-w0e5VMszC0xdK5bC1w&s" alt="Placeholder Image"></div>

            <form method="POST" action="">
                <!-- ID 입력 -->
                <label class="label" for="id">User ID</label>
                <input type="text" name="id" id="id" placeholder="input your ID" value="<?php echo htmlspecialchars($id); ?>" required /> 
                <?php if (!empty($error_messages['id'])): ?>
                    <div class="error-message" style="color: red; font-size: 0.75rem;"><?php echo $error_messages['id']; ?></div> <!-- 에러 메시지 출력 -->
                <?php endif; ?>

                <!-- 비밀번호 입력 -->
                <label class="label" for="pass">Password</label>
                <input type="password" name="pass" id="pass" placeholder="input your password" required /> 

                <!-- 비밀번호 확인 -->
                <label class="label" for="pass_confirm">Confirm Password</label>
                <input type="password" name="pass_confirm" id="pass_confirm" placeholder="double check your password" required /> 
                <?php if (!empty($error_messages['password'])): ?>
                    <div class="error-message" style="color: red; font-size: 0.75rem;"><?php echo $error_messages['password']; ?></div> <!-- 비밀번호 불일치 메시지 출력 -->
                <?php endif; ?>

                <!-- 이름 입력 -->
                <label class="label" for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="ex. 홍길동" value="<?php echo htmlspecialchars($name); ?>" required /> 

                <!-- 전화번호 입력 -->
                <label class="label" for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" placeholder="ex. 010-1234-5678 (optional)" value="<?php echo htmlspecialchars($phone); ?>" /> 
                <?php if (!empty($error_messages['phone'])): ?>
                    <div class="error-message" style="color: red; font-size: 0.75rem;"><?php echo $error_messages['phone']; ?></div> <!-- 에러 메시지 출력 -->
                <?php endif; ?>

                <!-- 이메일 입력 -->
                <label class="label" for="text">Email</label>
                <input type="text" name="email" id="email" placeholder="ex. asdf@gmail.com" value="<?php echo htmlspecialchars($email); ?>" required />  <!-- 이메일 유효성 검사 -->
                <?php if (!empty($error_messages['email'])): ?>
                    <div class="error-message" style="color: red; font-size: 0.75rem;"><?php echo $error_messages['email']; ?></div> <!-- 에러 메시지 출력 -->
                <?php endif; ?>
                
                <br /> <!-- 엔터 -->
                
                <label> 
                    <input type="checkbox" name="terms" required /> I agree to create an account
                </label>
                
                <div class="button-container" style="margin-top: 20px;">
                    <input type="button" class="back-button" value="Back to Menu" onclick="window.location.href='login.php'" /> <!-- Back to Menu 버튼 -->
                    <input type="submit" value="SIGN UP" /> <!-- SIGN UP 버튼 -->
                </div>
            </form>
        </div> <!-- 왼쪽 박스 끝 -->

        <div class="right-side"></div> 
    </div> <!-- 컨테이너 끝 -->
</body>
</html>
