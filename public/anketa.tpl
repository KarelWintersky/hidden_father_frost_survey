<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Анкета участника</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        *[required] {
            background-image: radial-gradient(#F00 15%, transparent 16%), radial-gradient(#F00 15%, transparent 16%);
            background-size: 1em 1em;
            background-position: right top;
            background-repeat: no-repeat;
            border-color: orange;
        }

        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .captcha {
            text-align: center;
            margin: 10px 0;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 15px;
            }

            button {
                padding: 8px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const session_values = JSON.parse('{$session|default:"{ }"}');

            Object.keys(session_values).forEach(function(key) {
                const input = document.querySelector(`[name='${ key }']`);
                if (input) {
                    input.value = session_values[key];
                }
            });
        });
    </script>
</head>
<body>

{if $state eq 'success'}
    ...
{else}

    <div class="form-container">
        <h2>Анкета участника движухи "Тайный Дед-Мороз"</h2>
        <form action="{Arris\AppRouter::getRouter('callback')}" method="POST">
            <input type="hidden" name="action" value="request">
            <label>
                <input type="text" name="name" placeholder="Имя" required>
            </label>
            <label>
                <input type="email" name="email" placeholder="Email" required>
            </label>
            <label>
                <textarea name="address" placeholder="Адрес" required></textarea>
            </label>

            <br><br>
            Я отправлю :
            <label>
                <select name="cards_count">
                    <option value="1" selected>1 открытку</option>
                    <option value="2">2 открытки</option>
                    <option value="3">3 открытки</option>
                </select>
            </label>
            <br><br>
            <hr>
            <br>

            <div class="captcha">
                <img src="captcha.php" alt="Капча">
                <label>
                    <input type="text" name="captcha" placeholder="Введите капчу" required>
                </label>
            </div>

            <label>
                <input type="checkbox" name="agreement" required>
                Я согласен/согласна на участие в розыгрыше
            </label>
            <br><br><br>

            <button type="submit">Отправить заявку</button>
        </form>
    </div>

{/if}

</body>
</html>