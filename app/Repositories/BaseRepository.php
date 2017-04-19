<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use Exception;

abstract class BaseRepository {

	/**
     * @var App
     */
    private $app;
 
    /**
     * @var
     */
    protected $model;
 
    /**
     * @param App $app
     *
     * @throws \Bosnadev\Repositories\Exceptions\RepositoryException
     */
    public function __construct(App $app) {

        $this->app = $app;

        $this->makeModel();

    }
 
    /**
     * Specify Model class name
     * 
     * @return mixed
     */
    abstract function model();
 
    /**
     * @return Model
     *
     * @throws RepositoryException
     */
    public function makeModel() {

        $model = $this->app->make($this->model());
 
        if (!$model instanceof Model) {
            throw new Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
 
        return $this->model = $model;

    }

}