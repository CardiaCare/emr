# Electronic medical record [![Build Status](https://travis-ci.org/dmeroff/emr.svg?branch=master)](https://travis-ci.org/dmeroff/emr)

## Requirements

- Composer
- PHP 7 and higher
- MariaDB

## Installation instructions

First of all, clone the repository and then `cd` into cloned directory.

### 1. Install dependencies using composer

Composer will download and install all dependencies needed by project.

```bash
composer install
```

### 2. Execute the init command and select environment

Execute the init command and select dev as environment (or prod if you're installing it for production). It will copy
local configs and entry script files.

```bash
php init
```

### 3. Update database schema

Create a new database and adjust the components['db'] configuration in config/common-local.php accordingly. Then you
can apply migrations:

```bash
php yii migrate
```

### 4. Install RBAC roles and permissions

```bash
php yii rbac/update
```

### 5. Set document roots of your web server

You need to set document root of your web server to `web` directory.
https://cardiacare.github.io/

