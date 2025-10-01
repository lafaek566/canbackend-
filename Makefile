build:
	cd container && docker compose build

setup:
	docker volume create --name=caraudio-mysql-data
	cd container && docker compose -p caraudio-container up -d

destroy:
	cd container && docker compose -p caraudio-container down

create-db:
	docker exec -i caraudio-db sh -c "MYSQL_PWD=root exec mysql -u root" < container/mysql/database.sql

import-db:
	docker exec -i caraudio-db sh -c "MYSQL_PWD=root exec mysql -u root" < database/db.sql

init:
	docker exec -ti php-caraudio make _init

_init:
	composer install
	cp .env.example .env
	php artisan key:generate
	php artisan migrate
	php artisan db:seed
