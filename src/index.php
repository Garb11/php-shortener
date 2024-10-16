<?php
define('LINKS_PATH', __DIR__ . '/data'); 
define('PARAM', 'hash'); 
define('LEN_HASH', 10);

$error_message = null;
$success_message  = null;

function isValidUrl($url) {
    $host = "http://{$_SERVER['HTTP_HOST']}";
    if (strpos($url, $host) !== false){
        return false;
    }
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200') !== false;
}

function generateHash() {
    return substr(md5(uniqid()), 0, LEN_HASH);
}

// обработка события кнопки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['url'])) {
    $url = $_POST['url'];

    // Проверка валидности ссылки
    if (isValidUrl($url)) {
        $hash = generateHash();
        $filepath = LINKS_PATH . "/$hash";
        file_put_contents($filepath, $url);
        $host = "http://{$_SERVER['HTTP_HOST']}";
        $success_message = "Сокращенная ссылка: <a href=\"?" . PARAM . "=$hash\">$host/?hash=$hash</a>";
    } else {
        $error_message = "Невалидная ссылка!";
    }
} 

// обработка запроса с параметром
if (!empty($_GET[PARAM])) {
    $hash = $_GET[PARAM];
    $url = null;
    $urlPath = LINKS_PATH . "/$hash";

    // если существует сокращение
    if (file_exists($urlPath)) {
        $url = file_get_contents($urlPath);
    }
    // действительный хэш
    if ($url) {
        header("Location: $url", true, 302);
        exit;
    // недействительный хэш
    } else {
        header("Location: /", true, 302);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сокращение ссылок</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="mt-4">Сократить ссылку</h1>
    <!-- Сообщение об ошибке -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message;  ?>
        </div>
    <?php endif; ?>
    <!-- Результат -->
    <?php if ($success_message): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    <!-- Форма -->
    <form method="POST" class="mt-2">
        <div class="form-group">
            <input type="url" name="url" class="form-control" placeholder="Введите ссылку" required>
        </div>
        <button type="submit" class="btn btn-primary">Сократить</button>
    </form>
</div>
</body>
</html>