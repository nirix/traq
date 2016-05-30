Traq
======

Traq is a PHP powered project manager and issue tracker featuring multiple projects,
milestones, custom fields, per-project permissions, email notifications and much more.

Requirements
------------

- PHP 5.5 or later
- Mcrypt
- Database supported by [Doctrine](http://www.doctrine-project.org)
  - PostgreSQL or MariaDB/MySQL recommended

If you're using Apache, rename `htaccess.txt` to `.htaccess`, otherwise you will
need to configure your server to use `index.php` as the 404 page.

Installation
------------

Open Traq in your browser and follow the installation steps.

### From Repository

```shell
composer install
cd dev
npm install
npm compile
```

After that is complete, open the path to Traq in a browser and follow the
installation steps.

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
