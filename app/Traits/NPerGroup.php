<?php

namespace App\Traits;

use DB;

trait NPerGroup {

  /**
   * query scope nPerGroup
   *
   * @return void
   */

  public function scopeNPerGroup($query, $relatedTable = NULL, $group, $n = 10, $order = null) {
    // queried table
    $table = ($this->getTable());

    $newQuery = $this->newQueryWithoutScopes();

    // initialize MySQL variables inline
    $newQuery->from(\DB::raw("(select @num:=0, @group:=0) as vars, {$table}"));
    $groupTable = $relatedTable ?: $table;

    // if no columns already selected, let's select *
    if (!$query->getQuery()->columns) {
      $newQuery->select("{$table}.*");
    }

    // make sure column aliases are unique
    $groupAlias = "{$table}_grp";//. md5(time());
    $numAlias = "{$table}_rn";// . md5(time());
    $numOpperation = !is_null($order) ? "@num" : "@num+1";

    // apply mysql variables
    $newQuery->addSelect(\DB::raw(
      "@num := if(@group = {$groupTable}.{$group}, {$numOpperation}, 1) as {$numAlias}, @group := {$groupTable}.{$group} as {$groupAlias}"
    ));

    if(is_null($order)) {
      $order = $group;
    }

    // make sure first order clause is the group order
    $newQuery->getQuery()->orders = (array) $query->getQuery()->orders;
    array_unshift($newQuery->getQuery()->orders, [
      'column' => "{$groupTable}.{$order}",
      'direction' => 'asc'
    ]);

    if ($relatedTable) {
      $newQuery->addSelect("{$groupTable}.{$group}");
      $newQuery->mergeBindings($query->getQuery());
      $newQuery->getQuery()->joins = (array) $query->getQuery()->joins;
      $query->whereRaw("{$table}.{$group} = {$groupTable}.{$group}");
    }

    // prepare subquery
    $subQuery = $query->toSql();
    $query->from(\DB::raw("({$newQuery->toSql()}) as {$table}"))
      ->where($numAlias, '<=', $n);

  }
}