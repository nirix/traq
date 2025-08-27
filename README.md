# Traq

Traq is a PHP powered project manager, capable of tracking issues for multiple projects with multiple milestones.

## Requirements

- PHP 8.3+
- MariaDB _(or MySQL)_
- Apache mod*rewrite *(or server configured to use `index.php` as the 404 page).\_
  - Rename `htaccess.txt` to `.htaccess` or find one that works on your server

## Building

After cloning the repository:

```
# Install PHP dependencies
composer install

# Install node dependencies
pnpm i

# Build UI
pnpm run build
```

## Installation

1. Upload Traq to a server
2. Open URL in browser
3. Follow installation steps

## Licenses

- Traq is released under the GNU GPL license, _version 3 only_.
- Avalon is released under the GNU Lesser GPL license, _version 3 only_.
- Nanite is released under the GNU Lesser GPL license, _version 3 only_.

### Terminated Licenses

See `TERMINATED_LICENCES.md`

## Contributors

A list of people who contribute or have contributed to Traq can be found on [Github](https://github.com/nirix/traq/graphs/contributors).
