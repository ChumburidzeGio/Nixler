Nixler Web App
=========

Web application for Nixler.

## Installation

Download repository from gitlab
```
git clone git@gitlab.com:nixler/web.git
```

Run `composer install` to pull down the latest version of the package. After composer downloaded all neccesery packages you can update the .env file. For generating fresh key use artisan command `php artisan key:generate`. After editing .env file set the right permissions on directories `storage` and `bootstrap/cache`. After run `php artisan install` and follow steps.

To run scheduled tasks we need to setup cron jobs. From the root of project run `pwd` to print full path to project and using `crontab -e` open the cron jobs file where wen need to add the line `* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1`.