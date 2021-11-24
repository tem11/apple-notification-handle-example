# apple-notification-handle-example

### Local installation instructions

#### Requirements
* Docker & Docker-compose installed on the dev machine

#### Instructions
* cp ./api/.env.dist ./api/env
* docker-compose up -d
* docker exec -it -u1000 test-app bash
  * composer install
  * bin/console doctrine:database:create
  * bin/console doctrine:migrations:migrate