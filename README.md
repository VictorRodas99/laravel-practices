<div align="center">

# Laravel API - Practices 🌱

<h3 style="font-style: italic">A basic API that allows creating customers and invoices and managing all the data, with operations for creation, deletion, and updating. All for the purpose of learning Laravel.</h3>

<hr>

</div>

## Project description

This project aims to create a basic API Rest.

## Technologies used
- [Composer](https://getcomposer.org/): Dependency manager for php
- [Laravel](https://laravel.com/): Backend Framework
- [MySQL](https://www.mysql.com/): Relational database management system

## Steps to set up the project

_You need to have MySQL and Laravel installed_
- _Laravel version: 10.8_
- _MySQL version: 8.0_

1. Clone the repository

```bash
git clone https://github.com/VictorRodas99/laravel-practices.git
```

2. Enter in the folder

```bash
cd laravel-practices/
```

3. Create a database and create an .env file based on .env.example

Edit the .env file with this:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=<mysql-port>
DB_DATABASE=<your-db-name>
DB_USERNAME=<db-user>
DB_PASSWORD=<your-password>
```

4. Install dependencies

```bash
composer install && npm install
```

5. Follow the steps of [jwt-auth-package-docs](https://jwt-auth.readthedocs.io/en/develop/laravel-installation/) to setup the jsonwebtoken authentication

6. Build js files for the swagger documentation

```bash
npm run build
```

7. Run the server

```bash
php artisan serve --port=8001
```

__It will start the server in:__ http://localhost:8001

__The api documentation should be in:__ http://localhost:8001/docs
