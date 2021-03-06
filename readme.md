# Тестовый проект на Laravel

В данном проекте реализована система обратной связи (в тексте используется термин "Обращение").

Существуют два типа учётных записей - _Менеджер_ и _Пользователь_.

**Пользователь** может создавать и просматривать свои обращения, а также скачивать прикреплённые файлы. 

**Менеджер** может оставлять ответы к обращениям, а так же видеть все обращения всех пользователей и скачивать любые прикреплённые файлы.

## Настройка и запуск

Для запуска потребуется:
* PHP 7.3;
* Composer;
* База данных (можно и локальная).

Чтобы запустить проект, нужно:
1. Спуллить репозиторий себе
2. Перейти в папку проекта
3. Выполнить ```composer install```
4. Скопировать ```.env.example``` в ```.env```
5. Внести правки в ```.env```, а именно указать доступы для базы данных
6. Произвести миграции с помощью ```php artisan migrate```
7. Создать основную учётную запись менеджера командой ```php artisan db:seed```
8. Запустить проект с помощью команды ```php artisan serve```
