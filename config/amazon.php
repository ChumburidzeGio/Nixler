<?php

return [

	/**
	 * Your access key.
	 */
	'access_key' => env('AMAZON_ACCESS_KEY', 'AKIAJOCLRPMDODV7TWPA'),

	/**
	 * Your secret key.
	 */
	'secret_key' => env('AMAZON_SECRET_KEY', 'sb1U4bxqfxiRdIvUH2eaztaI7Ulf4lm/GbK5vuUF'),

	/**
	 * Your affiliate associate tag.
	 */
	'associate_tag' => env('AMAZON_ASSOCIATE_TAG', 'nixler-20'),

	/**
	 * Preferred locale
	 */
	'locale' => env('AMAZON_LOCALE', 'com'),

	/**
	 * Preferred response group
	 */
	'response_group' => env('AMAZON_RESPONSE_GROUP', 'Images,ItemAttributes')


];