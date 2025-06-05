## Описание
Система распределенного выполнения CRON-задач с использованием:
- Laravel 10
- Docker
- MySQL 8


## Установка
```bash
git clone https://github.com/yanfeofanov/cron_app.git
cd cron_app
./start.sh
```

## Конфигурация
Отредактируйте `.env`:
```ini
DB_HOST=mysql
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=secret
```
При запуске ./start.sh возможно потребуются права
```
# sudo usermod -aG docker $USER
# newgrp docker
После чего запускается автомат 
 1) Пересобирается docker 
 2) Настраивает Laravel 
 3) Создает структуру БД 
 4) Дабовляет тестовую задачу 
 5) Запускает фоновой обработчик CRON 
 6) Выводит статус системы 
```
