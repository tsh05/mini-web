<?php
session_start();

$db_conn = mysqli_connect("localhost", "root", "root", "test_db");

if (!$db_conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['id'] ?? '';

    // 식별: 사용자 존재 여부 확인
    $sql = $db_conn->prepare("SELECT * FROM user WHERE id = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        // 인증: 사용자 존재
        $user = $result->fetch_assoc();

        $password = $_POST['pass'] ?? '';
        $hashedInputPassword = hash('sha256', $password); // 입력 비밀번호 해싱

        // 인증: 해시된 비밀번호 비교
        if ($user['pw'] === $hashedInputPassword) {
            $_SESSION['login_success'] = true; // 로그인 성공
            $_SESSION['username'] = $username; // 사용자 이름 저장
        } else {
            $_SESSION['login_success'] = false; 
            $_SESSION['message'] = "Wrong ID or password!";
        }
    } 

    header("Location: login.php"); // 로그인 페이지로 리다이렉트
    exit();
}

mysqli_close($db_conn);
?>
