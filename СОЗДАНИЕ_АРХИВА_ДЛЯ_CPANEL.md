# Создание архива для загрузки на cPanel

## Вариант 1: Через GitHub (Рекомендуется)

На сервере cPanel просто клонируйте репозиторий:

```bash
cd ~/public_html
git clone https://github.com/Ilyosxon2721/admin_kassa.git
cd admin_kassa
```

**Преимущества:**
- Быстро
- Не нужно создавать архив
- Легко обновлять (`git pull`)

---

## Вариант 2: Создание ZIP архива вручную

### Для Windows:

1. **Откройте проводник** в папке проекта: `d:\OSPanel\home\admin_kassa`

2. **Выделите следующие файлы и папки:**
   - ✅ `app/`
   - ✅ `bootstrap/`
   - ✅ `config/`
   - ✅ `database/`
   - ✅ `public/`
   - ✅ `resources/`
   - ✅ `routes/`
   - ✅ `storage/` (но БЕЗ содержимого logs/)
   - ✅ `tests/`
   - ✅ `vendor/`
   - ✅ `docker/`
   - ✅ `.github/`
   - ✅ Все файлы: `artisan`, `composer.json`, `package.json`, и т.д.
   - ✅ `.env.production`, `.env.testing`
   - ✅ Все `.md` файлы

3. **НЕ включайте:**
   - ❌ `node_modules/`
   - ❌ `.git/`
   - ❌ `.claude/`
   - ❌ `.osp/`
   - ❌ `.env` (создадите на сервере)
   - ❌ `storage/logs/*.log`
   - ❌ Файлы кэша в `storage/framework/`

4. **Создайте ZIP:**
   - Правая кнопка → Отправить → Сжатая ZIP-папка
   - Назовите: `admin_kassa_deploy.zip`

---

## Вариант 3: Через командную строку (для продвинутых)

### Windows PowerShell:

```powershell
# Перейдите в папку проекта
cd d:\OSPanel\home\admin_kassa

# Создайте архив (требует 7-Zip)
7z a -tzip admin_kassa_deploy.zip * `
  -xr!node_modules `
  -xr!.git `
  -xr!.claude `
  -xr!.osp `
  -xr!storage\logs\*.log `
  -xr!storage\framework\cache `
  -xr!storage\framework\sessions `
  -xr!storage\framework\views `
  -x!.env
```

### Git Bash (если установлен):

```bash
zip -r admin_kassa_deploy.zip . \
  -x "node_modules/*" \
  -x ".git/*" \
  -x ".claude/*" \
  -x ".osp/*" \
  -x "storage/logs/*.log" \
  -x "storage/framework/cache/*" \
  -x "storage/framework/sessions/*" \
  -x "storage/framework/views/*" \
  -x ".env"
```

---

## Что должно быть в архиве:

```
admin_kassa_deploy.zip
├── app/
│   ├── Events/
│   ├── Http/
│   ├── Listeners/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── build/      # ВАЖНО: должна быть собранная версия!
│   └── index.php
├── resources/
│   ├── js/
│   └── views/
├── routes/
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
├── tests/
├── vendor/         # ВАЖНО: должны быть установлены зависимости!
├── .env.production
├── .htaccess
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── vite.config.js
├── README.md
├── CPANEL_DEPLOYMENT.md
├── QUICK_START_CPANEL.md
└── другие .md файлы
```

---

## Проверка перед загрузкой:

### 1. ✅ Зависимости установлены:

```bash
# Composer
composer install --no-dev --optimize-autoloader

# NPM и сборка
npm install
npm run build
```

**Проверьте:**
- Папка `vendor/` существует и полная
- Папка `public/build/` существует с файлами

### 2. ✅ Размер архива:

Ожидаемый размер: **30-50 MB** (без node_modules и .git)

Если больше 100 MB - проверьте, что не включили:
- `node_modules/`
- `.git/`
- Логи

### 3. ✅ Структура:

Откройте ZIP и убедитесь, что файлы находятся в корне архива, а не во вложенной папке:

**Правильно:**
```
admin_kassa_deploy.zip
├── app/
├── public/
└── artisan
```

**Неправильно:**
```
admin_kassa_deploy.zip
└── admin_kassa/
    ├── app/
    ├── public/
    └── artisan
```

---

## После создания архива:

1. **Загрузите** через cPanel File Manager
2. **Распакуйте** в нужную директорию
3. **Следуйте** инструкциям в [CPANEL_DEPLOYMENT.md](CPANEL_DEPLOYMENT.md)

---

## Альтернатива: Прямая загрузка через FTP

Вместо создания архива можно загрузить файлы напрямую через FTP:

1. Подключитесь к серверу через FileZilla
2. Создайте папку `admin_kassa` на сервере
3. Загрузите ВСЕ файлы (кроме исключений выше)
4. Это займет больше времени, но избежите проблем с архивом

---

**Рекомендация:** Используйте **Git Clone** на сервере - это самый простой и быстрый способ!
