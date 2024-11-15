<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оповещение</title>
    {include file="templates/_favicons.tpl"}
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .notification {
            background-color: white;
            color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 300px;
        }

        .notification-success {
            background-color: #4CAF50;
        }

        .notification-problem {
            background-color: orange;
            color: black;
        }

        .notification h2 {
            margin: 0 0 10px;
        }

        .notification p {
            margin: 0;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const elements = document.querySelectorAll(`[data-action="redirect"]`);

            elements.forEach(function(element) {
                element.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    let url = element.getAttribute('data-url');
                    let target = element.getAttribute('data-target') || '';
                    let confirmMessage = element.getAttribute('data-confirm-message') || '';

                    if (confirmMessage.length > 0) {
                        if (!confirm(confirmMessage)) {
                            return false;
                        }
                    }

                    if (target === "_blank") {
                        const newWindow = window.open(url, '_blank');
                        if (newWindow) newWindow.focus(); // Проверяем, что новое окно успешно открылось
                    } else {
                        window.location.assign(url);
                    }
                });
            });
        });
    </script>
</head>
<body>

<div class="notification {if $state == 'success'} notification-success {else} notification-problem {/if}">
    {if $state == 'success'}
        <h2>Запрос принят!</h2>
        <p>Товарищ Вомбат сохранил ваши контактные данные.</p>
        <br><br>

        <p>Они будут обработаны и в середине декабря всем участникам будут разосланы адреса.</p>

        <br><br>
    {/if}

    {if $state = 'already'}
        <h2>Проблема!</h2>
        <p>Товарищ Вомбат обнаружил, что с вашего email-а уже оставляли заявку!</p>
        <br><br>
    {/if}

    <button
            type="button"
            data-action="redirect"
            data-url="{Arris\AppRouter::getRouter('view')}"
    >Назад, к форме</button>
</div>

</body>
</html>

