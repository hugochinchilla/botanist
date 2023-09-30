# Botanist 🪴

[![Run test suite](https://github.com/hugochinchilla/botanist/actions/workflows/run-phpunit.yml/badge.svg)](https://github.com/hugochinchilla/botanist/actions/workflows/run-phpunit.yml)

![banner](img/ls-example.png)

Add this plugin to your `composer.json` to prevent composer from creating files owned by root while running on docker.

Install this plugin and you will never have to fix a composer permission error again.

```bash
composer require hugochinchilla/botanist
```

## Why this project?

A pet peeve of mine is being able to execute any development environment just by running `docker compose up` after cloning
it. No need to set up anything, no need to read the readme file, for me that is devex bliss.

A typical problem setting docker to run with a user different that root is needing to customize your user id in a dotenv
file before being able to start the project, and that makes me unhappy.

## How does it work?

It sets a hook to run after commands that may write the `vendor/` dir or update the `composer.lock` file.
The hook will check the parent dir owner/group and will set the same ownership to the files.

It can only perform this action if composer is run as root, so if you don't install it as root you will not see
the ownership changed until you execute any install/update as root.


## Is it better than running docker as not root?

No. The best practice is to not run containers as root, but if you are running it as root nonetheless, this will solve
a common issue.

## What's the alternative?

You can use a mix of a dotenv file and the following snippet:

```
# .env
USER_ID=1000 # get yours running `id -u`

# docker-compose.yml
services:
  php:
    image: php:some-version
    user: ${USER_ID:-1000}:${USER_ID:-1000}
    ...
```

This will run the docker container as the same user as yours. If you don't define the USER_ID in the dotenv file
it will use 1000 as default value, there is a great chance that this is your id anyway.

But if you want to have a repo that can be cloned by any user and just run with `docker compose up` without any
prior setup, use botanist.
