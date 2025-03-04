# Laravel API

## Опис
Цей проєкт є RESTful API, розробленим на Laravel. API підтримує аутентифікацію через Sanctum, черги для обробки завдань, кешування та взаємодію з Redis і MySQL.

## Вимоги
- PHP 8.2+
- Composer
- Docker та Docker Compose
- MySQL або MariaDB

## Запуск у Docker

1. **Клонуйте репозиторій:**
   ```sh
   git clone https://github.com/bachinsky1/laravel-api.git
   cd laravel-api
   ```

2. **Створіть файл `.env` на основі прикладу:**
   ```sh
   cp src/.env.example src/.env
   ```

3. **Запустіть контейнери:**
   ```sh
   docker-compose up -d --build
   ```

4. **Виконайте установку Ларавель та бібліотек, міграції та сідінг бази даних:**
   ```sh
   docker-compose exec app composer install
   docker-compose exec app php artisan migrate --seed
   ```

## Основні маршрути API

### Аутентифікація
- `POST /api/register` — Реєстрація нового користувача
- `POST /api/login` — Вхід у систему
- `POST /api/logout` — Вихід із системи (вимагає аутентифікації)
- `GET /api/user` — Отримання даних поточного користувача (вимагає аутентифікації)

### Управління постами
- `GET /api/posts` — Отримати список постів
- `POST /api/posts` — Створити новий пост (вимагає аутентифікації)
- `GET /api/posts/{id}` — Отримати пост за ID
- `PUT /api/posts/{id}` — Оновити пост (вимагає аутентифікації)
- `DELETE /api/posts/{id}` — Видалити пост (вимагає аутентифікації)

### Інші маршрути
- `POST /api/send-email` — Відправка email через чергу
- `GET /api/weather` — Отримати погоду по місту
- `GET /api/admin` — Доступ до адмін-зони (вимагає роль адміністратора)

## Swagger документація
Swagger UI доступний за адресою:
```
http://localhost:8000/api/documentation
```
Щоб оновити документацію, виконайте команду:
```sh
docker-compose exec app php artisan l5-swagger:generate
```

## Управління чергами
API використовує Redis для обробки черг. Щоб запустити обробник черг:
```sh
docker-compose exec queue php artisan queue:work
```

## Доступ до бази даних
Ви можете отримати доступ до MySQL через **phpMyAdmin**:
```
http://localhost:8080
```
Логін: `root`
Пароль: `root`

## Перевірка роботи middleware
API логує всі запити у storage/logs/laravel.log. Щоб перевірити логування:
```
docker-compose exec app tail -f storage/logs/laravel.log
```
Для перевірки роботи middleware можна виконати будь-який запит до API та переглянути лог-файл.

## Запуск обробника черг
API використовує Redis для обробки черг:
```
docker-compose exec queue php artisan queue:work
```

## Перевірка Policies та Gates
Laravel використовує Gates та Policies для контролю доступу до маршрутів.

### Перевірка адміністративного доступу (/api/admin)
Створіть користувача з is_admin = false
Спробуйте отримати доступ до /api/admin
curl -X GET http://localhost:8000/api/admin -H "Authorization: Bearer {your_token}"
Очікуваний результат:
```
{
   "error": "Access Denied"
}
```
Встановіть is_admin = true для користувача в базі даних
Повторіть запит до /api/admin.
Очікуваний результат:
```
{
   "message": "Welcome, Admin"
}
```
