<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToMany;

use CoreWine\Component\Collection as BaseCollection;

class Collection extends BaseCollection{

    /**
     * The model field
     *
     * @var
     */
    protected $model;

    protected $persist_stack;

    public function __construct($array){
        parent::__construct($array);
        $this -> persist_stack = new BaseCollection();
        $this -> persist_stack['save'] = new BaseCollection();
    }

    /**
     * Set model field
     *
     * @param $model
     */
    public function setModel($model){
        $this -> model = $model;
    }

    /**
     * Get the model field
     *
     * @return Model
     */
    public function getModel(){
        return $this -> model;
    }


    public function addPersistStack($operation,$model){
        $this -> persist_stack[$operation][] = $model;
    }

    public function removePersistStack($operation,$model){
        $this -> persist_stack[$operation] -> remove($model);
    }

    public function persistStack($operation){

        foreach($this -> persist_stack[$operation] as $k){
            $k -> {$operation}();
        }
    }

    public function checkInstanceValueClass($value){
        $this -> getModel() -> checkInstanceValueClass($value);
    }

    /**
     * Retrieve index
     *
     * @param ORM\Model $value
     *
     * @return int
     */
    public function index($value){
        $this -> checkInstanceValueClass($value);

        foreach($this as $n => $k){
            if($k -> equalTo($value)){
                return $n;
            }
        }

        return false;
    }

    /**
     * Has a model ?
     *
     * @param ORM\Model $value
     *
     * @return boolean
     */
    public function has($value){
        $this -> checkInstanceValueClass($value);

        return $this -> index($value) ? true : false;
    }


    /**
     * Add a model
     *
     * @param ORM\Model $value
     */
    public function add($value){

        $this[] = $value;
    }
    
    public function offsetSet($index,$value){

        $this -> checkInstanceValueClass($value);

        if(!$this -> has($value)){

            $model = $this -> getModel();
            $resolver = $model -> getSchema() -> getResolver();


            $value -> {$resolver -> end -> field_to_start -> getName()} = $model -> getObjectModel();


            $this -> addPersistStack('save',$value);
            parent::offsetSet($index,$value);
        }
        
    }


    /**
     * Remove a model
     *
     * @param ORM\Model $value
     */
    public function remove($value){
        $index = $this -> index($value);
        unset($this[$index]);
    }


    public function offsetUnset($index){

        if(!$this -> exists($index))
            return;

        $value = $this -> items[$index];

        $this -> checkInstanceValueClass($value);

        $model = $this -> getModel();

        $resolver = $model -> getSchema() -> getResolver();

        $value -> {$resolver -> end -> field_to_start -> getName()} = null;

        $this -> addPersistStack('save',$value);
        parent::offsetUnset($index);

        
    }

    /**
     * Return all models
     *
     * @return this
     */
    public function all(){
        return $this;
    }

    /**
     * Save all
     */
    public function save(){
        $this -> persistStack('save');
    }

    /**
     * Sync
     */
    public function sync($values){
        
        foreach($values as $value){
            $this -> add($value);
        }

        $this -> save();
    }

    public function __toString(){
        $collection = [];

        foreach($this as $k){
            $collection[] = $k -> toArray();
        }

        return json_encode($collection);
    }
}