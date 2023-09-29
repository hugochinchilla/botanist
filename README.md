# stumpgrinder

[![Run test suite](https://github.com/hugochinchilla/stumpgrinder/actions/workflows/run-phpunit.yml/badge.svg)](https://github.com/hugochinchilla/stumpgrinder/actions/workflows/run-phpunit.yml)

![banner](img/ls-example.png)

Add this plugin to your `composer.json` to prevent composer from creating files owned by root while running on docker.

Run `composer require hugochinchilla/stumpgrinder` and you will never have to fix a composer permission error again.

## How does it work?

It sets a hook to run after commands that may write the `vendor/` dir or update the `composer.lock` file.
The hook will check the parent dir owner/group and will set the same ownership to the files.

It can only perform this action if composer is run as root, so if you don't install it as root you will not see
the ownership changed until you execute any install/update as root.
