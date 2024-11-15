# Secret Father Frost

Сбор данных для движухи "Тайный Дед Мороз"

# DEB

Package name: `hiddenfatherfrost`

# SQL

```sql

CREATE TABLE hiddenfatherfrost.participants (
	id INT auto_increment NOT NULL COMMENT 'id',
	fio varchar(100) DEFAULT '' NULL COMMENT 'ФИО',
	email varchar(100) DEFAULT '' NULL COMMENT 'email',
	address TEXT NULL COMMENT 'address',
	cards_count INT DEFAULT 1 NULL COMMENT 'cards_count',
	CONSTRAINT participants_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


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

```
put it to `/etc/arris/hidden_father_frost/site.ini`

