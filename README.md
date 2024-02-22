Данное приложение Symfony развернул используя Composer:
```bash
$ composer create-project symfony/skeleton:"7.0.*" symfony-project
```

Виртуальный хост настроил на папку /public

Все сущности и репозитории создал с помощью серии команд:
```bash
$ php bin/console make:entity
```

Создал файл миграции и затем сформировал из него структуру базы данных:
```bash
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
```

Логика взаимодействия статьи с категориями и подкатегориями следующая (есть три варианта):
1. статья может не относиться ни к какой категории или подкатегории; в таком случае в БД в таблице "article" столбцы "category_id" и "subcategory_id" оба будут иметь значение "NULL".
2. статья может относиться только к какой-нибудь категории, но не иметь подкатегорию; в таком случае в БД в таблице "article" столбец "subcategory_id" будет иметь значение "NULL".
3. статья может относиться к какой-нибудь категории и какой-нибудь подкатегории в этой категории; в таком случае в таблице "article" в столбец "subcategory_id" записывается это значение подкатегории, а столбец "category_id" оставляется пустым (значение "NULL"). Так как для каждой подкатегории всегда существует одна и только одна категория, к которой она относится, значение этой категории можно взять по внешнему ключу из таблицы "subcategory".

Заполнил базу данных "symfony" тестовыми данными так: через админку phpPgAdmin выполнил SQL-запрос к этой БД.
Тестовые данные для БД "symfony"."public" в файле "/symfony.sql"


Все CRUD-контроллеры создал с помощью серии команд (автоматически были созданы ещё формы и шаблоны):
```bash
php bin/console make:crud Article
php bin/console make:crud Category
php bin/console make:crud Subcategory
php bin/console make:crud User
```

Чтобы можно было войти в приложение как администратор, назначил хешированный пароль админу и изменил его роль на ROLE_ADMIN:
```php
UPDATE users SET password = '$2y$13$J/cM9DpoYfW0aHJhkla3ku7nY2etA3WBOghgnhDALT9dxNKL7iaiG', role = 'ROLE_ADMIN' WHERE id = 1;
```
Данные для входа в админку:
логин: admin
пароль: admin

