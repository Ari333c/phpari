<?php
session_start();

// Функция для генерации случайного года
function getRandomYear() {
    return rand(1930, 1950);
}

// Функция для проверки, кричит ли пользователь
function isYelling($message) {
    return substr(trim($message), -1) === "!";
}

// Инициализация счётчика прощаний
if (!isset($_SESSION['bye_count'])) {
    $_SESSION['bye_count'] = 0;
}

// Обработка сообщения
$response = "";
$user_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $user_message = trim($_POST['message']);
    
    // Добавляем сообщение в историю
    if (!isset($_SESSION['history'])) {
        $_SESSION['history'] = [];
    }
    
    // Проверяем на выход
    if ($user_message === "ПОКА!") {
        $_SESSION['bye_count']++;
        
        if ($_SESSION['bye_count'] >= 3) {
            $response = "ДО СВИДАНИЯ, МИЛЫЙ!";
            $_SESSION['bye_count'] = 0;
            session_destroy();
        } else {
            $response = "НЕТ, НИ РАЗУ С " . getRandomYear() . " ГОДА!";
        }
    } else {
        // Сбрасываем счётчик прощаний
        $_SESSION['bye_count'] = 0;
        
        // Обычный ответ
        if (isYelling($user_message)) {
            $response = "НЕТ, НИ РАЗУ С " . getRandomYear() . " ГОДА!";
        } else {
            $response = "АСЬ?! ГОВОРИ ГРОМЧЕ, ВНУЧЕК!";
        }
    }
    
    // Сохраняем в историю
    $_SESSION['history'][] = [
        'user' => $user_message,
        'granny' => $response,
        'time' => date('H:i:s')
    ];
}

// Обработка сброса
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Получаем историю
$history = $_SESSION['history'] ?? [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👵 Глухая бабушка</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #2c0a0a 0%, #4a1a1a 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: #fff5f5;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            overflow: hidden;
            border: 1px solid #8b3a3a;
        }

        .header {
            background: linear-gradient(135deg, #5c1a1a 0%, #2c0a0a 100%);
            color: #ffd7d7;
            padding: 30px;
            text-align: center;
            border-bottom: 3px solid #a55252;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            color: #ffb5b5;
        }

        .subtitle {
            font-size: 1.2em;
            opacity: 0.95;
            font-style: italic;
            background: rgba(70, 20, 20, 0.5);
            padding: 10px 20px;
            border-radius: 50px;
            display: inline-block;
            border: 1px solid #a55252;
        }

        .chat-container {
            background: #f0e0e0;
            padding: 20px;
        }

        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            background: #fff0f0;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: inset 0 2px 10px rgba(100, 20, 20, 0.1);
            border: 1px solid #d4a0a0;
        }

        .message {
            display: flex;
            margin-bottom: 20px;
        }

        .user-message {
            flex-direction: row-reverse;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dbb8b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 10px;
            border: 2px solid #8b3a3a;
        }

        .user-message .avatar {
            background: #5c1a1a;
            color: #ffd7d7;
            border: 2px solid #ffb5b5;
        }

        .granny-message .avatar {
            background: #8b3a3a;
            color: #ffd7d7;
            border: 2px solid #5c1a1a;
        }

        .message-content {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 20px;
            position: relative;
        }

        .user-message .message-content {
            background: linear-gradient(135deg, #6b2d2d 0%, #451a1a 100%);
            color: #ffd7d7;
            border-bottom-right-radius: 5px;
            border: 1px solid #a55252;
        }

        .granny-message .message-content {
            background: #ffe5e5;
            color: #2c0a0a;
            border-bottom-left-radius: 5px;
            border: 1px solid #b56b6b;
            box-shadow: 2px 2px 5px rgba(100, 20, 20, 0.1);
        }

        .message-sender {
            font-size: 0.8em;
            font-weight: bold;
            margin-bottom: 5px;
            opacity: 0.8;
        }

        .user-message .message-sender {
            color: #ffb5b5;
        }

        .granny-message .message-sender {
            color: #6b2d2d;
        }

        .message-text {
            font-size: 1.1em;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .message-time {
            font-size: 0.7em;
            text-align: right;
            margin-top: 5px;
            opacity: 0.7;
        }

        .user-message .message-time {
            color: #ffb5b5;
        }

        .granny-message .message-time {
            color: #6b2d2d;
        }

        .input-form {
            display: flex;
            gap: 10px;
            background: #ffe5e5;
            padding: 15px;
            border-radius: 50px;
            box-shadow: 0 5px 15px rgba(70, 20, 20, 0.2);
            border: 1px solid #b56b6b;
        }

        .input-form input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #d4a0a0;
            border-radius: 30px;
            font-size: 1em;
            outline: none;
            transition: all 0.3s;
            background: #fff5f5;
            color: #2c0a0a;
        }

        .input-form input:focus {
            border-color: #8b3a3a;
            box-shadow: 0 0 0 3px rgba(150, 50, 50, 0.2);
            background: #ffffff;
        }

        .input-form input::placeholder {
            color: #a87a7a;
        }

        .send-btn {
            padding: 15px 30px;
            background: linear-gradient(135deg, #6b2d2d 0%, #3d1515 100%);
            color: #ffd7d7;
            border: 1px solid #a55252;
            border-radius: 30px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .send-btn:hover {
            background: linear-gradient(135deg, #8b3a3a 0%, #5c1a1a 100%);
            box-shadow: 0 5px 15px rgba(100, 20, 20, 0.4);
            border-color: #ffb5b5;
            color: #ffffff;
        }

        .send-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 5px rgba(100, 20, 20, 0.4);
        }

        .hints {
            padding: 20px;
            background: #f0d8d8;
            border-top: 2px solid #b56b6b;
        }

        .hint-title {
            font-size: 1.1em;
            font-weight: bold;
            color: #2c0a0a;
            margin-bottom: 10px;
        }

        .hint-items {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .hint {
            background: #fff0f0;
            padding: 8px 16px;
            border-radius: 50px;
            color: #6b2d2d;
            font-size: 0.9em;
            box-shadow: 0 2px 5px rgba(100, 20, 20, 0.1);
            border: 1px solid #b56b6b;
            transition: all 0.3s;
        }

        .hint:hover {
            background: #ffe0e0;
            border-color: #8b3a3a;
            color: #2c0a0a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(100, 20, 20, 0.2);
        }

        .reset-section {
            padding: 0 20px 20px 20px;
            text-align: center;
            background: #f0d8d8;
        }

        .reset-btn {
            background: none;
            border: 2px solid #8b3a3a;
            color: #8b3a3a;
            padding: 10px 25px;
            border-radius: 30px;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }

        .reset-btn:hover {
            background: #8b3a3a;
            color: #ffd7d7;
            border-color: #ffb5b5;
        }

        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f0d8d8;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #8b3a3a 0%, #5c1a1a 100%);
            border-radius: 10px;
            border: 1px solid #ffb5b5;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #a55252;
        }

        @media (max-width: 600px) {
            .container {
                border-radius: 10px;
            }
            
            .header h1 {
                font-size: 1.8em;
            }
            
            .chat-messages {
                height: 300px;
            }
            
            .message-content {
                max-width: 85%;
            }
            
            .input-form {
                flex-direction: column;
                border-radius: 20px;
            }
            
            .send-btn {
                justify-content: center;
            }
            
            .hint-items {
                flex-direction: column;
            }
            
            .hint {
                text-align: center;
            }
        }

        .welcome-message {
            text-align: center;
            color: #8b3a3a;
            font-style: italic;
            padding: 20px;
            background: #ffefef;
            border-radius: 10px;
            margin: 10px 0;
            border: 1px dashed #b56b6b;
        }

        .bye-counter {
            font-size: 0.9em;
            color: #6b2d2d;
            text-align: right;
            padding: 5px 10px;
            background: #ffe5e5;
            border-radius: 20px;
            display: inline-block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Шапка -->
        <div class="header">
            <h1>👵 Глухая бабушка</h1>
            <p class="subtitle">ЧЕГО СКАЗАТЬ-ТО ХОТЕЛ, МИЛОК?!</p>
        </div>

        <!-- Основной чат -->
        <div class="chat-container">
            <!-- Окно чата -->
            <div class="chat-messages" id="chatMessages">
                <?php if (empty($history)): ?>
                    <div class="welcome-message">
                        ✨ Напиши что-нибудь бабушке... Но помни: она плохо слышит! ✨
                    </div>
                    <div class="message granny-message">
                        <div class="avatar">👵</div>
                        <div class="message-content">
                            <div class="message-sender">Бабушка</div>
                            <div class="message-text">ЧЕГО СКАЗАТЬ-ТО ХОТЕЛ, МИЛОК?!</div>
                            <div class="message-time">сейчас</div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($history as $msg): ?>
                        <!-- Сообщение пользователя -->
                        <div class="message user-message">
                            <div class="avatar">👤</div>
                            <div class="message-content">
                                <div class="message-sender">Вы</div>
                                <div class="message-text"><?php echo htmlspecialchars($msg['user']); ?></div>
                                <div class="message-time"><?php echo $msg['time']; ?></div>
                            </div>
                        </div>
                        <!-- Ответ бабушки -->
                        <div class="message granny-message">
                            <div class="avatar">👵</div>
                            <div class="message-content">
                                <div class="message-sender">Бабушка</div>
                                <div class="message-text"><?php echo htmlspecialchars($msg['granny']); ?></div>
                                <div class="message-time"><?php echo $msg['time']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Счётчик ПОКА! (для отладки) -->
            <?php if (isset($_SESSION['bye_count']) && $_SESSION['bye_count'] > 0): ?>
                <div class="bye-counter">
                    🗣️ Осталось сказать ПОКА! <?php echo 3 - $_SESSION['bye_count']; ?> раз(а)
                </div>
            <?php endif; ?>

            <!-- Форма ввода -->
            <form method="POST" action="" class="input-form" id="messageForm">
                <input 
                    type="text" 
                    name="message" 
                    id="messageInput"
                    placeholder="Напишите что-нибудь бабушке..." 
                    autocomplete="off"
                    required
                >
                <button type="submit" class="send-btn" id="sendBtn">
                    <span>📢</span> Сказать
                </button>
            </form>
        </div>

        <!-- Подсказки -->
        <div class="hints">
            <div class="hint-title">🎯 Бордовые подсказки:</div>
            <div class="hint-items">
                <span class="hint">• Не кричишь → "АСЬ?! ГОВОРИ ГРОМЧЕ!"</span>
                <span class="hint">• Кричи с "!" → случайный год (1930-1950)</span>
                <span class="hint">• Чтобы уйти, скажи ПОКА! 3 раза подряд</span>
                <span class="hint">• Не получилось? Нажми "Начать заново"</span>
            </div>
        </div>

        <!-- Кнопка сброса -->
        <div class="reset-section">
            <a href="?reset=1" class="reset-btn">🔄 Начать новый разговор</a>
        </div>
    </div>

    <script>
        // Функция для скролла вниз
        function scrollToBottom() {
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Запуск при загрузке страницы
        window.onload = function() {
            scrollToBottom();
            document.getElementById('messageInput').focus();
            
            // Добавляем эффекты для кнопки отправки (только при наведении)
            const sendBtn = document.getElementById('sendBtn');
            
            sendBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
            });
            
            sendBtn.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        };

        // Обработка отправки формы (без анимации)
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message) {
                e.preventDefault();
                messageInput.style.borderColor = '#ff0000';
                setTimeout(() => {
                    messageInput.style.borderColor = '#d4a0a0';
                }, 500);
            }
        });

        // Эффекты для подсказок
        document.querySelectorAll('.hint').forEach(hint => {
            hint.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            hint.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Обработка клавиши Enter
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('messageForm').requestSubmit();
            }
        });
    </script>
</body>
</html>
