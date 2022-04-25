## Technical Requirements
- Install PHP 7.2.5 or higher and these PHP extensions (which are installed and enabled by default in most PHP 7 installations): Ctype, iconv, JSON, PCRE, Session, SimpleXML, and Tokenizer;
- Install Composer, which is used to install PHP packages.
- Install Node.js and NPM
- Relation database (i use MySQL)
- Install Docker

## Step-by-step guide
- run `composer install` for installation php vendor
- edit .env file for database url ([reference](https://symfony.com/doc/5.4/doctrine.html#configuring-the-database))
- run `php bin/console doctrine:database:create` for create database
- run `php bin/console doctrine:migrations:migrate` for create database tables ([reference](https://symfony.com/doc/5.4/doctrine.html#migrations-creating-the-database-tables-schema))
- run `docker-compose up` for creating redis and elasticsearch
- run `php bin/console doctrine:fixtures:load` for create dummy data ([reference](https://symfony.com/doc/5.4/testing.html#load-dummy-data-fixtures))
- run `php bin/console fos:elastica:populate` for elasticsearch
- run `php -S localhost:8000 -t ./public/` for server
