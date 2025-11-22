<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Анкета участника движухи "Тайный Дед-Мороз"</title>
    <meta name="description" content="Хочешь быть в числе счастливчиков? Нет ничего проще: заполняешь анкету, выбираешь, сколько открыток ты готов отправить сам... и ждешь. Ждешь середины декабря, когда станет понятно, кого ты осчастливишь в этом году. ">

    {include file="templates/_opengraph.tpl"}
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
        /* радиокнопки вместо селекта */
        input[type="radio"] {
            transform: scale(1.1);
        }
        label {
            cursor: pointer;
            transition: color 0.2s ease;
        }
        label:hover {
            background-color: #f8f9fa;
            border-color: #4CAF50;
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
        <div style="font-size: xx-small; float: right">Version 1.0</div>
        <h2>Анкета участника движухи "Тайный Дед-Мороз"</h2>
        <div>
            <p>Чу! Вы слышите эти звуки? <br></p>
            <p>
                Колокольца звенят, колокольца звенят! <br>
                Это почтовые сани Деда Мороза несут поздравления писателям...
            </p>
            <p>
                Олени рвутся вперед из последних сил, спешат донести слова о музе, миллионных тиражах, миллиардных гонорарах.
                И ведь все сбудется, ведь это сказка, Новый год!
            </p>
            <p>
                Хочешь быть в числе счастливчиков? Нет ничего проще: заполняешь анкету, выбираешь, сколько открыток ты готов отправить сам... и ждешь.<br>
                Ждешь середины декабря, когда станет понятно, кого ты осчастливишь в этом году.
            </p>
            <p style="font-size: small">
                Как это работает? До <span style="color: #084298">полуночи {$event_end} г.</span> (но это неточно) система собирает ваши адреса и количество открыток, которые вы готовы отправить.
                Потом в автоматическом режиме вычисляется, кто кому {if $members_count}(одному из {$members_count}){/if} отправит открытку.
            </p>
        </div>
        <hr>
        <form action="{Arris\AppRouter::getRouter('callback')}" method="post">
            <input type="hidden" name="action" value="request">
            <label>
                <input type="text" name="fio" placeholder="Имя отправителя (для статистики)" required>
            </label>
            <label>
                <input type="email" name="email" placeholder="Email отправителя" required>
            </label>
            <label>
                <textarea name="address" placeholder="Укажите адрес, на который вы хотите получить открытку. " required></textarea>
            </label>

            <br><br>
            Я отправлю:

            {if $use_radio eq 0}

            &nbsp;
                <label>
                    <select name="cards_count">
                        <option value="1">1 открытку</option>
                        <option value="2" selected>2 открытки</option>
                        <option value="3">3 открытки</option>
                    </select>
                </label>
            <br>
            {else}

                <br><br>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label style="display: flex; align-items: center; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; transition: all 0.2s ease;">
                        <input type="radio" name="cards_count" value="1" style="margin: 0 10px 0 0;">
                        <span>1 открытку</span>
                    </label>
                    <label style="display: flex; align-items: center; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; transition: all 0.2s ease;">
                        <input type="radio" name="cards_count" value="2" checked style="margin: 0 10px 0 0;">
                        <span>2 открытки</span>
                    </label>
                    <label style="display: flex; align-items: center; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; transition: all 0.2s ease;">
                        <input type="radio" name="cards_count" value="3" style="margin: 0 10px 0 0;">
                        <span>3 открытки</span>
                    </label>
                </div>

            {/if}
            <br>
            <hr>
            <br>

            <div class="captcha">
                <img src="/captcha.php" alt="Капча" width="120" height="60" onclick="this.src='/captcha.php?r=' + Math.random(); return false;">
                <label>
                    <input type="text" name="captcha" placeholder="Введите символы с картинки" required>
                </label>
            </div>

            <label>
                <input type="checkbox" name="agreement" required>
                Я согласен/согласна на участие.
            </label>
            <br><br><br>

            <button type="submit">Отправить заявку</button>
        </form>
    </div>


</body>
</html>
