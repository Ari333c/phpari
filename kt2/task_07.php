<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$success = false;
$attempts_left = 3;

// Инициализация счетчика попыток
if (!isset($_SESSION['captcha_attempts'])) {
    $_SESSION['captcha_attempts'] = 3;
}

if (isset($_GET['new']) && $_GET['new'] === '1') {
    $_SESSION['captcha_code'] = (string) random_int(10000, 99999);
    $_SESSION['captcha_attempts'] = 3; // Сброс попыток при обновлении
    header('Location: task_07.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['captcha_input'])) {
    $input = trim($_POST['captcha_input'] ?? '');
    $expected = $_SESSION['captcha_code'] ?? '';
    
    if ($input !== '' && $expected !== '' && $input === $expected) {
        $success = true;
        $message = '✅ Верно! Капча решена правильно.';
        $_SESSION['captcha_attempts'] = 3; // Сброс попыток при успехе
        // Генерируем новый код после успешной проверки
        $_SESSION['captcha_code'] = (string) random_int(10000, 99999);
    } else {
        $_SESSION['captcha_attempts']--;
        $attempts_left = $_SESSION['captcha_attempts'];
        
        if ($attempts_left > 0) {
            $message = "❌ Неверный код. Осталось попыток: $attempts_left";
        } else {
            $message = '❌ Попытки исчерпаны. Обновите капчу.';
            // Блокируем форму, если попытки исчерпаны
            $_SESSION['captcha_blocked'] = true;
        }
    }
}

if (!isset($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = (string) random_int(10000, 99999);
}

$blocked = $_SESSION['captcha_blocked'] ?? false;
$attempts_left = $_SESSION['captcha_attempts'] ?? 3;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Задание 7: Капча-картинка</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-image: url('https://img.freepik.com/free-photo/view-mountain-with-dreamy-aesthetic_23-2151700198.jpg');
            min-height: 100vh;
        }
        
        .box {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .msg {
            margin: 15px 0;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .msg.ok {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .msg.err {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .captcha-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        
        img {
            display: block;
            margin: 0 auto 15px;
            border: 3px solid #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        img:hover {
            transform: scale(1.02);
        }
        
        .refresh-link {
            display: inline-block;
            margin: 10px 0;
            padding: 8px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .refresh-link:hover {
            background: #5a6268;
        }
        
        .form-group {
            margin: 20px 0;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #495057;
            font-weight: 500;
        }
        
        input[type="text"] {
            padding: 12px;
            width: 150px;
            border: 2px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            text-align: center;
            letter-spacing: 2px;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
        }
        
        input[type="text"]:disabled {
            background: #e9ecef;
            cursor: not-allowed;
        }
        
        button {
            padding: 12px 30px;
            background: linear-gradient(135deg, #ffffff 0%, #8000ff 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .attempts-info {
            margin-top: 15px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .attempts-bar {
            width: 100%;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 5px;
        }
        
        .attempts-progress {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #dc3545);
            border-radius: 2px;
            transition: width 0.3s;
        }
        
        .navigation {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
        }
        
        .nav-link {
            color: #667eea;
            text-decoration: none;
            padding: 5px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-link:hover {
            background: #f8f9fa;
        }
        
        .hint {
            background: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1> Задание 7: Капча-картинка</h1>
        <p>Введите код с картинки для подтверждения, что вы не робот.</p>

        <?php if ($message !== ''): ?>
            <div class="msg <?= $success ? 'ok' : 'err' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

       

        <div class="captcha-container">
            <img src="task_07_gen.php?t=<?= time() ?>" alt="Капча" width="200" height="70">
            
            <div>
                <a href="task_07.php?new=1" class="refresh-link"> Обновить капчу</a>
            </div>
        </div>

        <form method="POST" id="captchaForm">
            <div class="form-group">
                <label for="captcha_input">Введите код с картинки:</label>
                <input type="text" 
                       name="captcha_input" 
                       id="captcha_input"
                       autocomplete="off" 
                       maxlength="5" 
                       pattern="\d*"
                       title="Только цифры"
                       <?= $blocked ? 'disabled' : '' ?>
                       required>
            </div>
            
            <button type="submit" <?= $blocked ? 'disabled' : '' ?>>
                <?= $blocked ? '⛔ Доступ заблокирован' : '✅ Проверить' ?>
            </button>
        </form>

        <?php if (!$blocked): ?>
            <div class="attempts-info">
                <div>Осталось попыток: <?= $attempts_left ?></div>
                <div class="attempts-bar">
                    <div class="attempts-progress" style="width: <?= ($attempts_left / 3) * 100 ?>%"></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="navigation">
            <a href="task_06.php" class="nav-link">← Задание 6</a>
            <a href="task_05.php" class="nav-link">Задание 5 →</a>
        </div>
    </div>

    <script>
        // Автоматический переход к следующему полю (для мобильных устройств)
        document.getElementById('captcha_input').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^\d]/g, '');
            if (this.value.length === 5) {
                // Автоматическая отправка формы (опционально)
                // document.getElementById('captchaForm').submit();
            }
        });

        // Блокировка отправки пустой формы
        document.getElementById('captchaForm').addEventListener('submit', function(e) {
            const input = document.getElementById('captcha_input');
            if (input.value.length !== 5) {
                e.preventDefault();
                alert('Пожалуйста, введите 5 цифр');
            }
        });
    </script>
</body>
</html>
