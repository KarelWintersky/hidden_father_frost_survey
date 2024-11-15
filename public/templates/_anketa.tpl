<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Анкета участника</title>

    {include file="templates/_favicons.tpl"}

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
    <style>
        xz-notify {
            --xz-notify-title-color: currentColor;

            padding: 1em;
            border-radius: .25em;
            border-width: 1px;

            background: #cfe2ff;
            border-color: #b6d4fe;
            color: #084298;
        }
        xz-notify[type="success"] {
            background: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }
        xz-notify[type="info"] {
            background: #cff4fc;
            border-color: #b6effb;
            color: #055160;
        }
        xz-notify[type="warning"] {
            background: #fff3cd;
            border-color: #ffecb5;
            color: #664d03;
        }
        xz-notify[type="error"] {
            background: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
        }
    </style>
    <script type="module">
        import XZNotify from '/assets/xz-notify.min.js';
        document.addEventListener('DOMContentLoaded', function() {
            const flash_messages = {$flash_messages|json_encode|default:"{ }"};

            const xz_default_options = {
                expire: 10000,
                position: 'n',
                closeable: true
            };
            for (const message of flash_messages) {
                for (const [key, value] of Object.entries(message)) {
                    let notification = null;
                    switch (key) {
                        case 'success': {
                            notification = XZNotify.create(value, Object.assign({ }, xz_default_options, {
                                type: 'info'
                            }));
                            break;
                        }
                        case 'error': {
                            notification = XZNotify.create(value, Object.assign({ }, xz_default_options, {
                                type: 'error'
                            }));
                            break;
                        }
                        default: {
                            notification = XZNotify.create(value, Object.assign({ }, xz_default_options, {
                                type: 'debug'
                            }));
                            break;
                        }
                    }
                    document.body.appendChild(notification);
                }
            }

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
    <div class="form-container">
        <h2>Анкета участника движухи "Тайный Дед-Мороз"</h2>
        <form action="{Arris\AppRouter::getRouter('callback')}" method="post">
            <input type="hidden" name="action" value="request">
            <label>
                <input type="text" name="fio" placeholder="Имя отправителя (для учёта)" required>
            </label>
            <label>
                <input type="email" name="email" placeholder="Email отправителя (для учёта)" required>
            </label>
            <label>
                <textarea name="address" placeholder="Адрес получателя открытки (включая индекс)" required></textarea>
            </label>

            <br><br>
            Я отправлю:
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
                <img src="/captcha.php" alt="Капча" width="120" height="60" onclick="this.src='/captcha.php?r=' + Math.random(); return false;">
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


</body>
</html>
