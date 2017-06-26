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

### Allow multilingual dates
`locale -a` to check which locations are allowed, next do `sudo locale-gen id_ID.UTF-8` to install your locale and do `sudo dpkg-reconfigure locales` to publish it.
Reboot your system (`vagrant reload`) and enjoy


# Algorithm for Stream 

#### Basic

* User follows the seller - 20
* Category is Fashion or Techinics - 5
* Size of description contains more then 50 characters - 7
* Popularity of seller - 15
   * Has lot of followers
   * Sales
* Seller is from the same city - 8
* Amount of photos is more then 3 - 5
* Product don't have any photo - -20
* Seller is responsive to messages - 5

#### Statistics
* On like - 0.5
* One comment - 0.3
* One view - 0.1
* One sale - 2

# Algorithm for Similar Products 

#### Basic

* User follows the seller - 15
* Category is the same - 20
* Size of description contains more then 50 characters - 7
* Popularity of seller - 10
   * Has lot of followers
   * Sales
* Amount of photos is more then 3 - 5
* Product don't have any photo - -20
* Seller is responsive to messages - 5

#### Statistics
* On like - 0.5
* One comment - 0.3
* One view - 0.1
* One sale - 2

# Resources

* [Georgian Technical Dictionary](http://techdict.ge/)