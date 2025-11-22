# Secret Father Frost

Сбор данных для движухи "Тайный Дед Мороз"

# DEB

Package name: `hiddenfatherfrost`

# SQL

```sql

CREATE TABLE `participants` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
    `fio` varchar(100) DEFAULT '' COMMENT 'ФИО',
    `email` varchar(100) DEFAULT '' COMMENT 'email',
    `address` text DEFAULT NULL COMMENT 'address',
    `cards_count` int(11) DEFAULT 1 COMMENT 'cards_count',
    `event_year` int(11) DEFAULT 2024 COMMENT 'год события',
    PRIMARY KEY (`id`),
    KEY `participants_email_IDX` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

```

# INI

```ini
; год события
EVENT_YEAR = 2025
EVENT_END = "15 декабря 2024"

[DATABASE]
DRIVER		= "mysql"
HOST         	= "127.0.0.1"
PORT         	= 3306
USERNAME     	= "root"
PASSWORD     	= "password"
DBNAME       	= "hiddenfatherfrost"
CHARSET		= "utf8mb4"
COLLATE		= "utf8mb4_unicode_ci"

[REDIS]
; 8 because 'H' is eight letter of aplhabet
ENABLED = 1
DATABASE = 2

[FEATURES]
; использовать радиокнопки вместо селекта
USE_RADIO_BUTTONS = 1
```

put it to `/etc/arris/hidden_father_frost/site.ini`

# Nginx 

```txt
server {
    listen 80;
    server_name fatherfrost.local;

    root /var/www/hiddenfatherfrost/public/;

    index index.php index.html;

    error_log /var/log/nginx/hidden-father-frost.error.log;
    access_log /var/log/nginx/hidden-father-frost.access.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include         fastcgi_params;
        fastcgi_param   SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass    php-handler-8-2;
        fastcgi_index   index.php;
    }

    location ~ favicon.* {
        access_log      off;
        log_not_found   off;
    }

}

```

