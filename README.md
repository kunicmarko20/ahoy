Ahoy
====

[![Build Status](https://travis-ci.org/kunicmarko20/ahoy.svg?branch=master)](https://travis-ci.org/kunicmarko20/ahoy)

## Installation

**1.**  Clone Repository

```bash
git clone https://github.com/kunicmarko20/ahoy.git ahoy
```

**2.**  Install Dependencies

```bash
composer install
```

**3.**  Run Migrations

```bash
make dmm
```

**4.**  Create User

```bash
make user
```

**5.**  Start PHP Web server

```bash
php -S 127.0.0.1:8000 -t public
```

**6.**  Open

[http://127.0.0.1:8000/job-offer/create](http://127.0.0.1:8000/job-offer/create)

## Run Tests

```
make test
```

> You need phpunit installed globally.
