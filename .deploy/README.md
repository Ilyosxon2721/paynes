# 📁 Deployment Configuration Files

Эта папка содержит файлы для автоматизации деплоя на VPS и Laravel Forge.

## 🎯 Выберите метод деплоя:

1. **Laravel Forge** (рекомендуется) - см. `DEPLOY_FORGE.md` в корне
2. **Ручная настройка VPS** - см. `DEPLOY_VPS.md` в корне
3. **cPanel** - см. `DEPLOY.md` в корне

## 📄 Файлы

### 1. `server-setup.sh`
Скрипт первоначальной настройки сервера (выполняется ОДИН раз на новом сервере).

**Использование:**
```bash
# На сервере от root
sudo bash server-setup.sh
```

**Что делает:**
- Обновляет систему
- Устанавливает PHP 8.3, Nginx, MySQL, Redis, Node.js
- Устанавливает Composer, Supervisor
- Настраивает Firewall (UFW)
- Создает пользователя `deploy`

---

### 2. `deploy-vps.sh`
Скрипт автоматического деплоя (используется при каждом обновлении).

**Использование:**
```bash
# На сервере от пользователя deploy
cd ~/admin_kassa
bash .deploy/deploy-vps.sh
```

**Что делает:**
- Включает режим обслуживания
- Получает изменения из Git
- Устанавливает зависимости (composer, npm)
- Собирает frontend
- Выполняет миграции
- Очищает и создает кеши
- Перезапускает queue воркеры
- Выключает режим обслуживания

---

### 3. `nginx.conf`
Готовая конфигурация Nginx для Laravel.

**Установка:**
```bash
# Скопируйте и отредактируйте
sudo cp .deploy/nginx.conf /etc/nginx/sites-available/admin_kassa

# Измените yourdomain.com на ваш домен
sudo nano /etc/nginx/sites-available/admin_kassa

# Активируйте
sudo ln -s /etc/nginx/sites-available/admin_kassa /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### 4. `supervisor.conf`
Конфигурация Supervisor для Laravel Queue.

**Установка:**
```bash
# Скопируйте
sudo cp .deploy/supervisor.conf /etc/supervisor/conf.d/admin_kassa_worker.conf

# Примените
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start admin_kassa_worker:*
```

---

### 5. `forge-deploy.sh` 🆕
Оптимизированный скрипт деплоя для Laravel Forge.

**Использование в Forge:**
1. Откройте: **Sites** → Your Site → **Deployment**
2. Скопируйте содержимое `forge-deploy.sh`
3. Вставьте в поле **Deploy Script**
4. Нажмите **Save**

**Что делает:**
- Включает режим обслуживания
- Pull из Git
- Composer install (production)
- npm install и build
- Миграции
- Оптимизация кешей
- Перезапуск воркеров
- Выключает режим обслуживания

---

### 6. `forge-env.example` 🆕
Пример настроек окружения для Laravel Forge.

**Использование:**
1. Откройте в Forge: **Sites** → Your Site → **Environment**
2. Скопируйте содержимое из `forge-env.example`
3. Отредактируйте под свои нужды
4. Нажмите **Save**

---

## 🚀 Быстрый старт

### На новом сервере:

```bash
# 1. Подключитесь к серверу
ssh root@your_server_ip

# 2. Запустите setup скрипт
wget https://raw.githubusercontent.com/Ilyosxon2721/admin_kassa/main/.deploy/server-setup.sh
bash server-setup.sh

# 3. Настройте MySQL
sudo mysql_secure_installation

# 4. Переключитесь на пользователя deploy
su - deploy

# 5. Создайте SSH ключ
ssh-keygen -t ed25519 -C "deploy@server"
cat ~/.ssh/id_ed25519.pub  # Добавьте в GitHub

# 6. Клонируйте проект
git clone git@github.com:Ilyosxon2721/admin_kassa.git
cd admin_kassa

# 7. Установите зависимости
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build

# 8. Настройте .env
cp .env.production.example .env
nano .env  # Отредактируйте

# 9. Настройте базу данных
sudo mysql -u root -p
# CREATE DATABASE admin_kassa;
# CREATE USER 'paynes_user'@'localhost' IDENTIFIED BY 'password';
# GRANT ALL ON admin_kassa.* TO 'paynes_user'@'localhost';

# 10. Запустите миграции
php artisan key:generate
php artisan migrate --force

# 11. Настройте Nginx
sudo cp .deploy/nginx.conf /etc/nginx/sites-available/admin_kassa
sudo nano /etc/nginx/sites-available/admin_kassa  # Измените домен
sudo ln -s /etc/nginx/sites-available/admin_kassa /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# 12. Установите SSL
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com

# 13. Настройте Supervisor
sudo cp .deploy/supervisor.conf /etc/supervisor/conf.d/admin_kassa_worker.conf
sudo supervisorctl reread
sudo supervisorctl update

# 14. Настройте cron для Laravel Scheduler
crontab -e
# Добавьте: * * * * * cd ~/admin_kassa && php artisan schedule:run >> /dev/null 2>&1

# 15. Настройте права
sudo chown -R www-data:www-data ~/admin_kassa/storage ~/admin_kassa/bootstrap/cache
sudo chmod -R 775 ~/admin_kassa/storage ~/admin_kassa/bootstrap/cache
```

---

## 🔄 Обновление проекта

После каждого push в GitHub:

```bash
cd ~/admin_kassa
bash .deploy/deploy-vps.sh
```

---

## 🛠 Полезные команды

```bash
# Просмотр логов
tail -f storage/logs/laravel.log
sudo tail -f /var/log/nginx/admin_kassa_error.log

# Перезапуск сервисов
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
sudo supervisorctl restart admin_kassa_worker:*

# Очистка кешей
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Проверка статуса
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo supervisorctl status
```

---

---

## 🚀 Быстрый старт с Laravel Forge (Рекомендуется)

### 1. Создайте аккаунт Forge
https://forge.laravel.com - от $12/месяц

### 2. Подключите провайдера
- DigitalOcean (рекомендуется)
- Linode, Vultr, AWS, Hetzner

### 3. Создайте сервер
- PHP 8.3
- MySQL 8.0
- Redis
- Node.js

### 4. Создайте сайт
- Репозиторий: `Ilyosxon2721/paynes`
- Ветка: `main`

### 5. Настройте окружение
Используйте `forge-env.example`

### 6. Настройте деплой
Используйте `forge-deploy.sh`

### 7. Нажмите "Deploy Now"
Готово! 🎉

**Подробная инструкция:** См. `DEPLOY_FORGE.md` в корне проекта

---

## 📚 Дополнительная документация

- **Laravel Forge**: `DEPLOY_FORGE.md` - Простой деплой за 15 минут
- **Ручная настройка VPS**: `DEPLOY_VPS.md` - Полный контроль
- **cPanel**: `DEPLOY.md` - Для shared хостинга
