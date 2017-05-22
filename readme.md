Nixler Web App
=========

Web application for Nixler.

## Installation

Download repository from gitlab
```
git clone git@gitlab.com:nixler/web.git
```

Run `composer install` to pull down the latest version of the package. After composer downloaded all neccesery packages you can update the .env file. For generating fresh key use artisan command `php artisan key:generate`. After editing .env file set the right permissions on directories `storage` and `bootstrap/cache`. After run `php artisan install` and follow steps.