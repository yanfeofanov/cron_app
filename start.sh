#!/bin/bash
# 0. Доп. функция (обработка пользователя сперва добавляем права)
# sudo usermod -aG docker $USER
# newgrp docker
# 1. Остановка и пересборка контейнеров
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# 2. Установка зависимостей
docker-compose exec app composer install

# 3. Настройка окружения
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate

# 4. Выполнение миграций
docker-compose exec app php artisan migrate:fresh --force

# 5. Добавление тестовой CRON-задачи
docker-compose exec app php artisan tinker --execute='
\App\Models\CronTask::create([
    "command" => "test:example",
    "schedule" => "* * * * *",
    "description" => "Test task"
]);
'

# 6. Запуск обработчика CRON
echo "Запускаем обработчик CRON..."
docker-compose exec -d app php artisan cron:process

# 7. Проверка статуса
echo "Проверяем работу системы:"
docker-compose ps
docker-compose exec mysql mysql -uroot -psecret laravel -e "SELECT * FROM cron_tasks;"
