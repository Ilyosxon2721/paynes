#!/bin/bash

###############################################################################
# VPS Initial Setup Script
# Project: Paynes - Admin Kassa
# Description: Автоматическая настройка сервера Ubuntu 22.04/24.04
###############################################################################

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
log_warning() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

# Проверка root прав
if [ "$EUID" -ne 0 ]; then
    log_error "Запустите скрипт с правами root: sudo bash server-setup.sh"
    exit 1
fi

log_info "========================================="
log_info "  VPS Server Setup for Laravel"
log_info "========================================="
echo ""

# Шаг 1: Обновление системы
log_info "Шаг 1/12: Обновление системы..."
apt update && apt upgrade -y
log_success "Система обновлена"

# Шаг 2: Установка базовых пакетов
log_info "Шаг 2/12: Установка базовых пакетов..."
apt install -y software-properties-common curl wget git unzip zip
log_success "Базовые пакеты установлены"

# Шаг 3: Установка PHP 8.3
log_info "Шаг 3/12: Установка PHP 8.3..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring \
    php8.3-curl php8.3-xml php8.3-bcmath php8.3-intl \
    php8.3-redis php8.3-opcache
log_success "PHP 8.3 установлен"

# Шаг 4: Установка Composer
log_info "Шаг 4/12: Установка Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
log_success "Composer установлен"

# Шаг 5: Установка Node.js
log_info "Шаг 5/12: Установка Node.js 20.x..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
log_success "Node.js установлен"

# Шаг 6: Установка Nginx
log_info "Шаг 6/12: Установка Nginx..."
apt install -y nginx
systemctl start nginx
systemctl enable nginx
log_success "Nginx установлен и запущен"

# Шаг 7: Установка MySQL
log_info "Шаг 7/12: Установка MySQL..."
apt install -y mysql-server
systemctl start mysql
systemctl enable mysql
log_success "MySQL установлен"

# Шаг 8: Установка Redis
log_info "Шаг 8/12: Установка Redis..."
apt install -y redis-server
sed -i 's/supervised no/supervised systemd/' /etc/redis/redis.conf
systemctl restart redis
systemctl enable redis
log_success "Redis установлен"

# Шаг 9: Установка Supervisor
log_info "Шаг 9/12: Установка Supervisor..."
apt install -y supervisor
systemctl enable supervisor
log_success "Supervisor установлен"

# Шаг 10: Настройка PHP для production
log_info "Шаг 10/12: Настройка PHP..."
cat >> /etc/php/8.3/fpm/php.ini <<EOF

; Custom Laravel Production Settings
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 512M
max_execution_time = 300
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
EOF

systemctl restart php8.3-fpm
log_success "PHP настроен"

# Шаг 11: Настройка Firewall
log_info "Шаг 11/12: Настройка UFW..."
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
echo "y" | ufw enable
log_success "Firewall настроен"

# Шаг 12: Создание пользователя deploy
log_info "Шаг 12/12: Создание пользователя deploy..."
if id "deploy" &>/dev/null; then
    log_warning "Пользователь deploy уже существует"
else
    useradd -m -s /bin/bash deploy
    usermod -aG sudo deploy
    log_success "Пользователь deploy создан"
    log_warning "Установите пароль: passwd deploy"
fi

# Итоговая информация
echo ""
log_success "========================================="
log_success "  ✅ Сервер настроен успешно!"
log_success "========================================="
echo ""
log_info "Установленные версии:"
php -v | head -n 1
composer --version | head -n 1
node --version
npm --version
nginx -v 2>&1 | head -n 1
mysql --version | head -n 1
redis-server --version | head -n 1
echo ""
log_info "Следующие шаги:"
echo "1. Настройте MySQL: sudo mysql_secure_installation"
echo "2. Создайте SSH ключ: su - deploy && ssh-keygen -t ed25519"
echo "3. Добавьте ключ в GitHub: cat ~/.ssh/id_ed25519.pub"
echo "4. Клонируйте проект: git clone git@github.com:Ilyosxon2721/admin_kassa.git"
echo "5. Настройте Nginx конфиг из .deploy/nginx.conf"
echo "6. Установите SSL: sudo certbot --nginx -d yourdomain.com"
echo ""
