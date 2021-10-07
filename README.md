# SCiO dataSCRIBE

The dataSCRIBE project.

## REQUIREMENTS

The project requires **[PHP 7.4/8.0](https://www.php.net/manual/en/install.php)** as well as **[Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)** to be installed.

## CLONE THE REPOSITORY

    git clone git@github.com:NoeticBlue/scio-datascribe-backend.git

## INSTALL THE DEPENDENCIES

    cd scio-datascribe-backend && composer install

## CONFIGURE

    cp .env.example .env

## RUN THE BACKEND

    ./vendor/bin/sail up -d

## STOP THE BACKEND

    ./vendor/bin/sail down

## GENERATE A KEY AND A JWT SECRET

    ./vendor/bin/sail artisan key:generate
    ./vendor/bin/sail jwt:secret

## RUN THE MIGRATIONS

To destroy the existing database and start fresh:

    ./vendor/bin/sail artisan migrate:fresh

## SEED THE DATABASE

To seed the database with test data:

    ./vendor/bin/sail artisan db:seed

**NOTE**: In order to be able to seed the data, you need to modify your **.env** file and set your application environment to the following `APP_ENV=local` or `APP_ENV=development`.

### LINK THE STORAGE FOR FILES

To link the storage for files:

    ./vendor/bin/sail artisan storage:link

**NOTE**: The storage for files should be linked in order for the backend to be able to serve files publicly.

### CHANGE THE STORAGE DRIVER FOR S3

To change the storage driver, add the following settings:

    FILESYSTEM_DRIVER=s3

    AWS_ACCESS_KEY_ID="your-access-key-id"
    AWS_SECRET_ACCESS_KEY="your-secret-key"
    AWS_DEFAULT_REGION="your-region"
    AWS_BUCKET="your-bucket-name"

### CHANGE THE QUEUE DRIVER TO REDIS

To change the queue driver to redis, add the following settings:

    QUEUE_CONNECTION=redis
    REDIS_HOST=redis

## RUN TESTS

To execute the test suites run:

    ./vendor/bin/pest

## VIEW TEST COVERAGE

To view the test coverage report:

    ./vendor/bin/pest --coverage

## AUTOCOMPLETION

If you add a dependency, `_ide_helper.php` will be auto-regenerated. If you change a model definition please run:

    ./vendor/bin/sail artisan ide-helper:models

and type `no` to update the model autocompletion file (`_ide_helper_models.php`).
