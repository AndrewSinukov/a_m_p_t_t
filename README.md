The Amo Media Project Test Task
==============

# Как это должно работать в идеале / работает  ?

Для этого проекта хорошо подходит архитектура CQRD + Command Bus.
В начале сохранять в редис в мастер. После создавать евент для смежных событий ложить его в очередь, и отслеживать его.
После читать или демоном или кроном из слейва редиса и писать в мастер Mysql. В иделале импортировать CSV через LOAD DATA INFILE.
При коммите, создавать ивент(на удаление перемещенных ранее данных из редиса), ложить в брокер, и отслеживать его.
Так же, важно читать логи и отслеживать нагрузку(в случае нескольких нод). 
Для этого подойдет ELK / EFK, Grafana, Prometheus... может что еще есть полезное.
   
* `redis`: В начале данные сохраняются в редис,
* `db`: Кроновской коммандой, читаются из редиса, пишуться в Mysql, а после удаляются из списка в редисе. Не стал перекладывать удаление из редиса  в очередь.

Ссылка на скрины:

```bash
https://drive.google.com/open?id=1-0VBOa4qEEHDlvxc8-0XIBHSLZysiwV0
```
# Installation

Docker:

```bash
docker-compose build
docker-compose up -d
```

Composer:
```bash
composer install
```

Doctrine:
```bash
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

Установленный бандл для крона:
После накатывания миграций. Создастся cron_job таблица, в нее можно добавить крон задачу через консоль, обращаясь к бандлу через комманду bin/console cron:create. Ссылка ниже. 
Либо добавить:
INSERT INTO `cron_job` (`id`, `name`, `command`, `schedule`, `description`, `enabled`) VALUES
(1, 'amo_media_add_profiles', 'amo:media:add:profiles', '*/5 * * * *', 'Creates profiles and stores them in the database', 1);

И каждые 5 минут будет запускатся комманда.

```bash
https://github.com/Cron/Symfony-Bundle
https://tecadmin.net/crontab-in-linux-with-20-examples-of-cron-schedule/
```