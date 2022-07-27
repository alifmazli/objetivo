# Objetivo

![Objetivo Login Screen]("resources/img/objetivo-login-screen.png")

## Description

Objetivo is a task management web app built using the Laravel framework, alongside HTML/CSS, Bootstrap and JavaScript. Users can execute full CRUD functionalities on "goals", "tasks", "action plans", and "users". 

Users can assign a mentor to a particular goal by inviting another user via email; and users are automatically labelled as a mentor once invited by another user. Objetivo users can simultaneously be a mentor or a mentee on different goals.

You may run the app locally using the steps listed at the [this](https://github.com/alifmazli/objetivo/edit/main/README.md#running-objetivo) section below.

## Prerequisites

1. All that Laravel 9 requires
2. NodeJS
3. MySQL/MariaDB

## Setup and Running Objetivo

You may run the app via following commands:

```bash
$ git clone https://github.com/alifmazli/objetivo.git
$ cd objetivo
$ composer i # download dependencies
$ npm i # download dependencies
$ cp .env.example .env
$ php artisan key:generate
$ php artisan migrate # to setup the database
$ php artisan db:seed # to populate with fake data
$ npm run watch # to build the JS assets
```

## Contributors

1. [Daniel](https://github.com/keluhingbavui)
2. [Syaamil](https://github.com/escornbar)
3. [Khairina](https://github.com/kyuuurina)
4. [Athirah](https://github.com/athirahlokman)
5. [Sarah](https://github.com/sarahatiqah)
6. [Aiman](https://github.com/xputerax)
7. [Alif](https://github.com/alifmazli) (Me!)
