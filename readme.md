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

# Styleguide

### Routing
In the name of routes repository name should be the first, then by dots seperated other identifiers. Examples:
* collections.create
* collections.update
* collections.store
* collections.delete
* collections.follow
* collections.productSearch

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

# Capsules

#### StreamCapsule
```php
$capsule = capsule('stream')
	->whereSeller($user->id)
	->whereLikeBy($user->id)
	->recommendedFor($user->id)
	->wherePrice(200, 1400)
	->whereCategory(1)
	->whereIds([1])
	->whereInCollection($collection->id)
	->search(null)
	->perPage(500)
	->latest()
	->popular();

$capsule->keys();

$capsule->get()->items(); //or just $capsule->items();
$capsule->get()->nextPageUrl(); //or just $capsule->nextPageUrl();
$capsule->get()->priceFacet(); //or just $capsule->priceFacet();
$capsule->get()->categories(); //or just $capsule->categories();

$capsule->get()->toArray();
$capsule->get()->toJson();
```

#### RecoCapsule
```php
$userStream = capsule('reco')->forUser($user)->get();

$similarProducts = capsule('reco')->forProduct($product)->get();
```

# Underscore

#### List
```html
<div class="_lst">
	<div class="_lsti">Item</div>
	<div class="_lsti active">Item</div>
	<div class="_lsti">
		<i class="material-icons">icon</i> Item
	</div>
</div>
```

#### Card
```html
<div class="_crd">
	<div class="_crd-header">Title</div>
	<div class="_crd-content">
		Body
	</div>
</div>
```

#### Loader
```html
<div class="_ldr">Loading...</div>
```

# Code quality

#### Check php files
```bash
vendor/bin/churn run {path}
```

# Style Guide

#### Names of routes and controllers
Each Model should have seperate folder in controllers folder, name should following:
* Index
* Create
* Show
* Edit
* Save
* Destroy

# Resources

* [Georgian Technical Dictionary](http://techdict.ge/)
* [CSS Hues](https://webkul.github.io/coolhue/)
* [Javascript Comments Reindention](https://forum.sublimetext.com/t/javascript-indentation-broken-after-comment/13609/8)