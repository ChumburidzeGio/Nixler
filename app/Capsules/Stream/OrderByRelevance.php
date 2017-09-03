<?php

namespace App\Capsules\Stream;

trait OrderByRelevance {

	private function relevancyGeneralAlgorithm()
	{
		return [
			'(p.id / 100)', //Latest
			'(p.likes_count * 0.5)', //Frequently likes
			'(p.sales_count * 5)', //Frequnetly sold
			'(p.views_count / 100)', //Frequnetly viewed
			'IF((p.category_id < 9 && p.price < 300), (300 - p.price) * 0.1, 0)',
			'IF((p.category_id < 9 && p.price > 300), -50, 0)',
			'IF(p.original_price IS NOT NULL, 40, 0)',
			'IF(p.target IN ("men", "women", "unia"), 30, 0)',
			'IF(p.in_stock < 3, -10, 0)'
		];
	}

	private function relevancySimilarAlgorithm()
	{
		$general = $this->relevancyGeneralAlgorithm();

		return array_merge([
			'IF(p.category_id = ?, 30, 0)',
			'IF(p.owner_id = ?, 20, 0)',
			'((MATCH (owner_username,slug,title,description,p.currency,buy_link,sku,target) AGAINST (? IN NATURAL LANGUAGE MODE)) * 30)',
		], $general);
	}

	public function relevant($type = 'general', $params = [])
	{
		$name = 'relevancy'.ucfirst($type).'Algorithm';

		$algorithm = implode(' + ', $this->$name());

		$this->model->selectRaw("({$algorithm}) as SCORE", $params)->orderBy('SCORE', 'desc');

		return $this;
	}

}