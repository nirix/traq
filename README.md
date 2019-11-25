Traq
======

Traq is a PHP powered project manager and issue tracker featuring multiple projects,
milestones, custom fields, per-project permissions, email notifications and much more.

[![Build Status](https://travis-ci.org/nirix/traq.svg?branch=master)](https://travis-ci.org/nirix/traq)

Requirements
------------

- PHP 7.2 or later
- Database
  - MariaDB/MySQL
  - PostgreSQL

You will need to configure your server to use `public/index.php` as the 404 page.

Installation
------------

Open Traq in your browser and follow the installation steps.

### From Repository

```shell
composer install
yarn
yarn dev
cp .env.example .env
php artisan key:generate
```

After that is complete, ener your details into the `.env` file and open the path to Traq in a browser and
follow the installation steps.

Alternatively you can run the migrations seed the development data:

```shell
php artisan migrate --seed
```

Translations
------------

Traq is developed in English but is easily translated to other languages.

Any translations for Traq should be submitted to the [forum](https://forum.traq.io).
This is to keep unmaintained/outdated translations out of the repository.

Licenses
-------

* Traq is released under the GNU GPL license, _version 3 only_. See the `COPYING`
  file for more information.
* Avalon is released under the Apache License 2.0.

### Terminated Licenses ###

Licenses _permanently_ terminated:

* **devxdev / Devon Hazelett**:
  Files, classes and functions were taken and completely stripped of copyright,
  warranty and code comments then used in the "Soule Framework".

* **burnpiro / Kemal Erdem and michalantoszczuk**:
  Traq was forked and all references to Traq in each files copyright headers was
  removed and replaced with "Shelu".

Contributors
------------

A list of people who contribute or have contributed to Traq can be found on
[Github](https://github.com/nirix/traq/graphs/contributors).
