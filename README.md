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

Students project:
[https://se.cs.petrsu.ru/wiki/%D0%A0%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D0%B0_%D0%BF%D1%80%D0%BE%D1%82%D0%BE%D1%82%D0%B8%D0%BF%D0%B0_%D0%BC%D0%B5%D0%B4%D0%B8%D1%86%D0%B8%D0%BD%D1%81%D0%BA%D0%BE%D0%B9_%D0%B8%D0%BD%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%86%D0%B8%D0%BE%D0%BD%D0%BD%D0%BE%D0%B9_%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D1%8B_%D0%B8_%D1%81%D1%80%D0%B5%D0%B4%D1%81%D1%82%D0%B2_%D0%B8%D0%BD%D1%82%D0%B5%D0%B3%D1%80%D0%B0%D1%86%D0%B8%D0%B8_%D0%B2_%D0%B8%D0%BD%D1%82%D0%B5%D0%BB%D0%BB%D0%B5%D0%BA%D1%82%D1%83%D0%B0%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5_%D0%BF%D1%80%D0%BE%D1%81%D1%82%D1%80%D0%B0%D0%BD%D1%81%D1%82%D0%B2%D0%BE:_%D0%94%D0%BE%D0%BA%D1%83%D0%BC%D0%B5%D0%BD%D1%82_%D0%BF%D1%80%D0%BE%D0%B5%D0%BA%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F#.D0.90.D1.83.D1.82.D0.B5.D0.BD.D1.82.D0.B8.D1.84.D0.B8.D0.BA.D0.B0.D1.86.D0.B8.D1.8F_.D0.B2_.D1.81.D0.B8.D1.81.D1.82.D0.B5.D0.BC.D0.B5]

