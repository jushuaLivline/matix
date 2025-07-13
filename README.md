# Laravel Sail Installation Guide

This document provides instructions for setting up Laravel Sail in a Laravel 9.52 project.

## Prerequisites

- Docker Desktop installed and running
- Docker Compose installed
- For Windows users: WSL2 (Windows Subsystem for Linux 2) installed and enabled

## Installation in an Existing Laravel 9.52 Project

### Step 1: Install Sail via Composer

```bash
composer require laravel/sail --dev
```

### Step 2: Publish the Sail Configuration

Run the sail:install Artisan command to publish Sail's docker-compose.yml file to the root of your application:

```bash
php artisan sail:install
```

During installation, you'll be prompted to choose which services you want to include with Sail. Common options include:
- MySQL
- PostgreSQL
- Redis
- Memcached
- Meilisearch
- MinIO
- Mailpit
- Selenium

Select the services that your application requires.

### Step 3: Start Sail

After installation, start Sail using:

```bash
./vendor/bin/sail up
```

To run Sail in detached mode (in the background):

```bash
./vendor/bin/sail up -d
```

### Step 4: Configure a Bash Alias (Optional)

For convenience, you may want to set up a Bash alias for the Sail command:

```bash
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```

Add this to your shell configuration file (~/.bashrc, ~/.zshrc, etc.).

## Additional Configuration Options

### Using DevContainer

If you would like to develop within a DevContainer, you can use the `--devcontainer` option:

```bash
php artisan sail:install --devcontainer
```

This will publish a default .devcontainer/devcontainer.json file to the root of your application.

### Customizing Sail

To customize Sail further, you can publish its Dockerfiles:

```bash
php artisan sail:publish
```

This places the Dockerfiles and other configuration files in a `docker` directory in your application's root.

## Accessing Your Application

Once Sail is running, you can access your application at:

```
http://localhost
```

## Adding phpMyAdmin to Your Setup

To add phpMyAdmin to your Sail environment, you can modify the `docker-compose.yml` file. Add the following service configuration:

```yaml
phpmyadmin:
    image: 'phpmyadmin/phpmyadmin'
    ports:
        - '${FORWARD_PHPMYADMIN_PORT:-8084}:80'
    environment:
        PMA_HOST: mysql
        MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
        PMA_USER: '${DB_USERNAME}'     
        PMA_PASSWORD: '${DB_PASSWORD}'
        UPLOAD_LIMIT: 1G
        MAX_EXECUTION_TIME: 600
        MEMORY_LIMIT: 1G
        POST_MAX_SIZE: 1G
        UPLOAD_MAX_FILESIZE: 1G
    networks:
        - sail
    depends_on:
        - mysql
```

Add this to your `docker-compose.yml` file under the `services` section. Once added, restart Sail to apply the changes:

```bash
sail down
sail up -d
```

You can then access phpMyAdmin at `http://localhost:8084`. The default port is 8084, but you can change this by setting the `FORWARD_PHPMYADMIN_PORT` variable in your `.env` file.

## Complete docker-compose.yml Example

Below is a complete example of a `docker-compose.yml` file with Laravel, MySQL, and phpMyAdmin services:

```yaml
services:
    laravel.test:
        build:
            context: './vendor/laravel/sail/runtimes/8.4'
            dockerfile: Dockerfile
            args:
                WWWGROUP: '${WWWGROUP}'
        image: 'sail-8.4/app'
        extra_hosts:
            - 'host.docker.internal:host-gateway'
        ports:
            - '${APP_PORT:-80}:80'
            - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
        environment:
            WWWUSER: '${WWWUSER}'
            LARAVEL_SAIL: 1
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
            XDEBUG_CONFIG: '${SAIL_XDEBUG_CONFIG:-client_host=host.docker.internal}'
            IGNITION_LOCAL_SITES_PATH: '${PWD}'
        volumes:
            - '.:/var/www/html'
        networks:
            - sail
        depends_on:
            - mysql
    mysql:
        image: 'mysql/mysql-server:8.0'
        ports:
            - '${FORWARD_DB_PORT:-3306}:3306'
        environment:
            MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
            MYSQL_ROOT_HOST: '%'
            MYSQL_DATABASE: '${DB_DATABASE}'
            MYSQL_USER: '${DB_USERNAME}'
            MYSQL_PASSWORD: '${DB_PASSWORD}'
            MYSQL_ALLOW_EMPTY_PASSWORD: 1
            MYSQL_GRANT_PRIVILEGES: 'true'
        volumes:
            - 'sail-mysql:/var/lib/mysql'
            - './vendor/laravel/sail/database/mysql/create-testing-database.sh:/docker-entrypoint-initdb.d/10-create-testing-database.sh'
        networks:
            - sail
        healthcheck:
            test:
                - CMD
                - mysqladmin
                - ping
                - '-p${DB_PASSWORD}'
            retries: 3
            timeout: 5s
    phpmyadmin:
        image: 'phpmyadmin/phpmyadmin'
        ports:
            - '${FORWARD_PHPMYADMIN_PORT:-8084}:80'
        environment:
            PMA_HOST: mysql
            MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
            PMA_USER: '${DB_USERNAME}'     
            PMA_PASSWORD: '${DB_PASSWORD}'
            UPLOAD_LIMIT: 1G
            MAX_EXECUTION_TIME: 600
            MEMORY_LIMIT: 1G
            POST_MAX_SIZE: 1G
            UPLOAD_MAX_FILESIZE: 1G
        networks:
            - sail
        depends_on:
            - mysql
networks:
    sail:
        driver: bridge
volumes:
    sail-mysql:
        driver: local
```

## Common Sail Commands

- Start Sail: `sail up`
- Start Sail in background: `sail up -d`
- Stop Sail: `sail down`
- Run Artisan commands: `sail artisan <command>`
- Run Composer commands: `sail composer <command>`
- Run npm commands: `sail npm <command>`
- View logs: `sail logs`
- Run tests: `sail test`

For more detailed information, refer to the [official Laravel Sail documentation](https://laravel.com/docs/9.x/sail).