[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)

# Collab'media

## Description
Collab'media provides to your organization a powerful and handy way to manage social media publications by involving the whole team.

### Features

- Complete roles and authorizations management
- Proposals rating and reviewing
- Social media integration and publication scheduling

## Getting started

### Prerequisites

You need to install some software to run the application:

- [Composer](https://getcomposer.org/)
- [Docker CE](https://www.docker.com/community-edition)
- [Docker Compose](https://docs.docker.com/compose/install)

### Install composer dependencies

Run the following command to install composer dependencies:

```bash
composer install
```

#### Init

```bash
docker-compose up -d
```
> Notice: Check the `docker-compose.yml` file content. If other containers use the same ports, change yours.

### Access

You should access to:

- Application: [http://127.0.0.1:8000](http://127.0.0.1:8000)
- phpMyAdmin: [http://127.0.0.1:8080](http://127.0.0.1:8080)
- MailHog: [http://127.0.0.1:8025](http://127.0.0.1:8025)
> Notice: If you're using Docker Toolbox, change 127.0.0.1 by the IP address of your virtual machine, ie 192.168.99.100

### Fake data

Fake data is automatically imported when you start docker-compose. If you want to change it, you have to edit the `/sql/import.sql` file.
You can login

- as user with `user1@example.com` to `user12@example.com`
- as reviewer with `reviewer1@example.com` to `reviewer5@example.com`
- as publisher with `publisher1@example.com` to `publisher3@example.com`
- as admin with `admin@example.com`

For all the demo accounts, the password is `password`

