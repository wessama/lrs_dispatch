# Biotech Router

A microservice for communicating with third-party vendor applications based on Lumen 8.

### Installation

-   Clone the Repo:
    -   `git clone https://github.ibm.com/standalone/biotech-router`
-   `cd biotech-router`
-   `composer install`
-   `php artisan key:generate`
-   `php artisan jwt:secret`
-   `php artisan migrate`
-   `php artisan serve`

#### Create new user

-   `php artisan ti`
-   `factory('App\Models\User')->create(['email' => 'admin@localtest.me', 'password' => 'password'])`

### Configuration

-   `mv .env.example .env`
-   Edit `.env` file for database connection configuration.

### Issues

Please create an issue if you find any bug or error.

### License

MIT

Licensed Materials - Property of IBM
6949-70Y
(c) Copyright IBM Corp. 2020
US Government Users Restricted Rights - Use, duplication or disclosure restricted by GSA ADP Schedule Contract with IBM Corp.

