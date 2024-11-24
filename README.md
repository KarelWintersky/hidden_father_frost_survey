# Secret Father Frost

Сбор данных для движухи "Тайный Дед Мороз"

# DEB

Package name: `hiddenfatherfrost`

# SQL

```sql

CREATE TABLE `participants` (
    `id` int NOT NULL AUTO_INCREMENT COMMENT 'id',
    `fio` varchar(100) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT 'ФИО',
    `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT 'email',
    `address` text COLLATE utf8mb4_general_ci COMMENT 'address',
    `cards_count` int DEFAULT '1' COMMENT 'cards_count',
    PRIMARY KEY (`id`),
    KEY `participants_email_IDX` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


```

# INI

```ini
[database]
DB.DRIVER       = "mysql"
DB.HOST         = "127.0.0.1"
DB.PORT         = 3306
DB.USERNAME     = "root"
DB.PASSWORD     = "password"
DB.NAME         = "hiddenfatherfrost"
DB.CHARSET	    = "utf8mb4"
DB.COLLATE	    = "utf8mb4_unicode_ci"

[mailer]
MAILER.SMTP.USERNAME = ''
MAILER.SMTP.PASSWORD = ''

[redis]
REDIS.ENABLED = 1
REDIS.DATABASE = 8

; 8 because 'H' is eight letter of aplhabet


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

