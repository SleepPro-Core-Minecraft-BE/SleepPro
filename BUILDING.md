# Сборка SleepPro PUBLIC

Нужны PHP 8.1+ с расширениями из `composer.json`, Composer и Git.
Обычного системного PHP без расширений ядра недостаточно.

```sh
composer install --no-dev --prefer-dist
php -d phar.readonly=0 build/server-phar.php --out=SleepPro-0.1-PUBLIC.phar
./start.sh -p /path/to/compatible/php
```

Конфигурация создаётся в `settings.yml`. При переходе со старого сервера
сначала сохраните резервную копию и вручную перенесите настройки из `pocketmine.yml`.
Не используйте плагины или vendor от партнёрской сборки для сборки PUBLIC.
