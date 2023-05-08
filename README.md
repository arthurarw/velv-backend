# Setup Project

---
### Step by step

Clone the repo

```sh
git clone https://github.com/arthurarw/velv-backend.git
```

Create the .env file

```sh
cp .env.example .env
```

Update environment variables on .env file
```dosini
APP_NAME="Backend"
APP_URL=http://localhost:8989

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Up the project containers
```sh
docker-compose up -d
```

Access the container
```sh
docker-compose exec app bash
```

Install the dependencies

```sh
composer install
```

Generate the key project

```sh
php artisan key:generate
```

The "database" of project is in

```sh
storage/app/db/servers.xlsx
```

After the complete installation of the project, run the following command:

```sh
php artisan app:refresh-servers-command
```

This command will populate the data;

On your system you need to put this cron command:

```sh
* * * * * cd /PROJECT_FOLDER && docker-compose exec -it app php artisan app:refresh-servers-command
```

This command will update the data every 1 minute.

---

### Tests

To run the tests, inside the container, run the following command:

```sh
php artisan test
```

---

Access the project on local:

[http://localhost:8989](http://localhost:8989)

Link on production

[http://ec2-18-230-138-180.sa-east-1.compute.amazonaws.com:3000/](http://ec2-18-230-138-180.sa-east-1.compute.amazonaws.com:3000/)
