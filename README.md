# stumpgrinder

Add this plugin to your `composer.json` to make all files created by composer while run as root use the owner of the parent directory.

Tired of running composer in a Docker container and having a mix of file ownerships in your repo? That problem is over.

Run `composer require hugochinchilla/stumpgrinder` and you will never have to fix a composer permission error again. 