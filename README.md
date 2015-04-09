# Development Environment

This branch is dedicated to the development environment of the project. All work to do on this point has to be merge on this branch first, before being merged on other branches.

The purpose of this environment is to ease the development process. We preconfigured a Virtual Machine just for you. The only thing you have to do is to install the requirements and the deployment will be done with only one command.

### Requirements

* [Vagrant](http://docs.vagrantup.com/)
* [Ansible](http://docs.ansible.com/)

# Getting started

## Install

* Install the requirements
* Start the Vagrant VM: `vagrant up`

## Usage

* HTTP access: `localhost:8080`
* SSH access: `vagrant ssh`
* MySQL access:
    * user : `root`
    * no password
    * remote access: specify `127.0.0.1` as host (port `3306`) (requires MySQL client)
* redis access:
    * remote access allowed (requires redis client)
    * host: `127.0.0.1`
    * port: `6379`

If you wish to reload configuration: `vagrant provision`

If you need to execute a root command, you can either use `sudo` or login as root the root user with `su` (password:
`vagrant`).

If you wish to change the port used because they overlap with already used ports or whatever your reason is, check
the configuration in the `Vagrantfile`.

# Default configuration

The VM container is configured via Ansible:

* Latest version of [Jessie](https://www.debian.org/releases/jessie/index.en.html) (Debian 8)
* [Wget](http://www.gnu.org/software/wget/) & [cURL](http://curl.haxx.se/)
* [nginx](http://nginx.org/)
* [Git](http://git-scm.com/)

PHP Environment:
* [PHP5.6](http://php.net/)
* [PHP CLI](http://www.php-cli.com/)
* [PHP5 FPM](http://php-fpm.org/)
* [Pear](http://pear.php.net/)
* [Composer](https://getcomposer.org/)
* [Mcrypt](http://php.net/manual/fr/book.mcrypt.php)
* [Xdebug](http://xdebug.org/)
* [PsySH](http://psysh.org/)

Databases:
* [Redis](http://redis.io/)
* [MySQL](https://www.mysql.fr/)

Git:
* Aliases
* Default push method set to `current`
* Global gitignore preconfigured to ignore `linux`, `intelliJ`, `NetBeans` and `Eclipse` files
* For more: `git config --global --list`

Shell aliases: run `alias` to see the available aliases.

# TODO

* nginx
    * Configure nginx vhost for Symfony with HTTPS in dev mode and redirection of HTTPS to HTTP.
