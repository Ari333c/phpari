<?php
session_start();
$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInput = strtoupper(trim($_POST['captcha'] ?? ''));
    $result = ($userInput === ($_SESSION['captcha'] ?? '')) ? 'Правильно' : 'Не правильно';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('https://img.freepik.com/premium-photo/close-up-monkeys-sitting-outdoors_1048944-20541537.jpg');
            font-family: Arial, sans-serif;
        }
        
        .captcha-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            min-width: 350px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
        }
        
        .captcha-image {
            margin: 20px auto;
            display: flex;
            justify-content: center;
        }
        
        .captcha-image img {
            border: 2px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin: 20px 0;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #ffffff;
        }
        
        button {
            background: linear-gradient(135deg, #ffffff 0%, #000000 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .result {
            margin-top: 20px;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
        }
        
        .result.correct {
            background: #d4edda;
            color: #155724;
        }
        
        .result.wrong {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="captcha-container">
        <h1>Регистрация</h1>
        
        <div class="captcha-image">
            <img src="captcha.php?<?php echo time(); ?>" alt="CAPTCHA">
        </div>
        
        <form method="post">
            <div class="form-group">
                <label>Введите строку:</label>
                <input type="text" name="captcha" required autocomplete="off">
            </div>
            <button type="submit">OK</button>
        </form>
        
        <?php if ($result): ?>
            <div class="result <?php echo $result === 'Правильно' ? 'correct' : 'wrong'; ?>">
                <?php echo $result; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
