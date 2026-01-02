# 🐳 Docker Setup - Anesi Kassa

Полное руководство по запуску Anesi Kassa с Docker.

---

## 📋 Требования

- Docker 20.10+
- Docker Compose 2.0+
- 2GB свободного места

---

## 🚀 Быстрый старт

### 1. Клонировать проект

```bash
git clone https://github.com/your-org/anesi-kassa.git
cd anesi-kassa
```

### 2. Настроить переменные окружения

```bash
cp .env.example .env
```

Отредактируйте `.env`:
```env
DB_HOST=mysql
DB_DATABASE=admin_kassa
DB_USERNAME=anesi
DB_PASSWORD=secret

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
```

### 3. Запустить Docker контейнеры

```bash
docker-compose up -d
```

Это запустит:
- **app** - PHP 8.3-FPM (Laravel приложение)
- **nginx** - Nginx веб-сервер (порт 8000)
- **mysql** - MySQL 8.0 (порт 3306)
- **redis** - Redis (порт 6379)
- **node** - Node.js для сборки фронтенда

### 4. Установить зависимости

```bash
# Composer зависимости
docker-compose exec app composer install

# Сгенерировать ключ
docker-compose exec app php artisan key:generate

# Запустить миграции
docker-compose exec app php artisan migrate

# Заполнить тестовыми данными
docker-compose exec app php artisan db:seed
```

### 5. Собрать фронтенд

```bash
# Установить npm зависимости (выполняется автоматически в node контейнере)
# Или вручную:
docker-compose exec node npm install
docker-compose exec node npm run build
```

### 6. Открыть приложение

Откройте в браузере: **http://localhost:8000**

Тестовые учетные данные:
- Логин: `admin`
- Пароль: `admin123`

---

## 🛠️ Полезные команды

### Docker управление

```bash
# Запустить контейнеры
docker-compose up -d

# Остановить контейнеры
docker-compose down

# Перезапустить контейнеры
docker-compose restart

# Просмотр логов
docker-compose logs -f

# Просмотр логов конкретного сервиса
docker-compose logs -f app

# Статус контейнеров
docker-compose ps
```

### Laravel команды

```bash
# Войти в контейнер app
docker-compose exec app bash

# Artisan команды
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:list

# Tinker
docker-compose exec app php artisan tinker

# Тесты
docker-compose exec app php artisan test
```

### MySQL

```bash
# Войти в MySQL
docker-compose exec mysql mysql -uanesi -psecret admin_kassa

# Backup базы данных
docker-compose exec mysql mysqldump -uanesi -psecret admin_kassa > backup.sql

# Restore базы данных
docker-compose exec -T mysql mysql -uanesi -psecret admin_kassa < backup.sql
```

### Redis

```bash
# Войти в Redis CLI
docker-compose exec redis redis-cli

# Очистить кэш
docker-compose exec redis redis-cli FLUSHALL
```

### Frontend

```bash
# Development mode
docker-compose exec node npm run dev

# Production build
docker-compose exec node npm run build

# Watch mode
docker-compose exec node npm run watch
```

---

## 📁 Структура Docker

```
anesi-kassa/
├── docker/
│   ├── nginx/
│   │   └── conf.d/
│   │       └── default.conf      # Nginx конфигурация
│   └── php/
│       └── local.ini              # PHP настройки
├── docker-compose.yml             # Docker Compose конфигурация
└── Dockerfile                     # PHP-FPM образ
```

---

## 🔧 Конфигурация сервисов

### App (PHP-FPM)

- **Image:** Custom (PHP 8.3-FPM + расширения)
- **Port:** 9000 (внутренний)
- **Volume:** `./:/var/www`

### Nginx

- **Image:** nginx:alpine
- **Port:** 8000 (внешний) → 80 (внутренний)
- **Volume:** `./:/var/www`, `./docker/nginx/conf.d:/etc/nginx/conf.d`

### MySQL

- **Image:** mysql:8.0
- **Port:** 3306
- **Volume:** `mysql-data:/var/lib/mysql` (persistent)
- **Env:**
  - `MYSQL_DATABASE=admin_kassa`
  - `MYSQL_USER=anesi`
  - `MYSQL_PASSWORD=secret`

### Redis

- **Image:** redis:alpine
- **Port:** 6379

### Node

- **Image:** node:18-alpine
- **Command:** `npm install && npm run dev`
- **Volume:** `./:/var/www`

---

## 🐛 Troubleshooting

### Порт уже занят

Если порт 8000 занят, измените в `docker-compose.yml`:

```yaml
nginx:
  ports:
    - "8080:80"  # Измените на другой порт
```

### Ошибка подключения к MySQL

Подождите несколько секунд после запуска контейнеров - MySQL требует времени для инициализации.

### Права доступа к файлам

```bash
docker-compose exec app chmod -R 755 storage bootstrap/cache
docker-compose exec app chown -R anesi:anesi storage bootstrap/cache
```

### Очистка и перезапуск

```bash
# Остановить и удалить все контейнеры
docker-compose down

# Удалить volumes (ВНИМАНИЕ: удалит данные БД!)
docker-compose down -v

# Пересоздать все контейнеры
docker-compose up -d --build
```

---

## 🔄 Development vs Production

### Development (текущая конфигурация)

Оптимизирована для разработки:
- Live reload
- Подробное логирование
- Debug mode
- Volumes для live editing

### Production

Для production измените:

1. **Оптимизация PHP:**
```dockerfile
# В Dockerfile добавить
RUN docker-php-ext-install opcache
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/
```

2. **.env для production:**
```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
```

3. **Nginx SSL:**
Добавьте SSL сертификаты и настройте HTTPS.

---

## 📊 Мониторинг

### Проверка здоровья

```bash
# Health check endpoint
curl http://localhost:8000/api/health

# Статус контейнеров
docker-compose ps

# Использование ресурсов
docker stats
```

### Логи

```bash
# Все логи
docker-compose logs -f

# Laravel логи
docker-compose exec app tail -f storage/logs/laravel.log

# Nginx access log
docker-compose logs -f nginx

# MySQL логи
docker-compose logs -f mysql
```

---

## 🎯 Следующие шаги

1. Настройте CI/CD для автоматического деплоя Docker образов
2. Добавьте Laravel Horizon для управления очередями
3. Настройте мониторинг с Prometheus + Grafana
4. Добавьте Traefik для автоматического SSL

---

## 📞 Поддержка

При возникновении проблем:

1. Проверьте логи: `docker-compose logs`
2. Убедитесь что все контейнеры запущены: `docker-compose ps`
3. Проверьте `.env` конфигурацию
4. Смотрите основной [README.md](README.md)

---

**Happy Coding! 🚀**
