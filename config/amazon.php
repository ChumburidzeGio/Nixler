<?php

return [

	/**
	 * Your access key.
	 */
	'access_key' => env('AMAZON_ACCESS_KEY', 'AKIAILXHNNWRYH7LITXA'),

	/**
	 * Your secret key.
	 */
	'secret_key' => env('AMAZON_SECRET_KEY', 'smNvKqzFJcd1r95fWWLbhmsFMaaCfS8ippyrRcQK'),

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