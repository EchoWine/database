<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToMany;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * has value raw
	 */
	public $has_value_raw = false;

	/**
	 * Is the value updated
	 *
	 * @var bool
	 */
	public $value_updated = false;

	/**
	 * Check if value has the same class as defined in resolver
	 *
	 * @param ORM\Model
	 *
	 * @return bool
	 */
	public function checkInstanceValueClass($model){
		if(get_class($model) != $this -> getSchema() -> getResolver() -> end -> model){
			throw new \Exception($this -> getSchema() -> getResolver() -> end -> model." != ".get_class($model));
		}
	}

	/**
	 * Set value
	 */
	public function setValueOut($value = null){

		if($value == null)
			$value = [];


		$c = new Collection($value);

		$c -> setModel($this);

		parent::setValueOut($c);
	}


	/**
	 * get the value raw from repository
	 *
	 * @return mixed
	 */
	public function getValueRawFromRepository($row,$relations = []){
		
		return null;
	}


	/**
	 * get the value raw from repository
	 *
	 * @return mixed
	 */
	public function getValueOutFromRepository($row,$relations = []){
		
		$value = [];

		return $value;
	}


	/**
	 * Retrieve a value out given a value raw
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function getValueOutByValueRaw($value){

		$resolver = $this -> getSchema() -> getResolver();

		# Field start Model
		$start_field_identifier = $this -> getObjectModel() -> getField($resolver -> start -> field_identifier -> getName());



		# Get all result of resolver: Mid table
		if(!$start_field_identifier -> getValue()){

			$this -> value_updated = false;
			return null;
		}


		return $resolver -> end -> model::where($resolver -> end -> field_to_start -> getColumn(),$start_field_identifier -> getValue()) -> get();
	}





	/**
	 * Get the value
	 *
	 * @return mixed
	 */
	public function getValue(){

		if(($this -> getValueOut() == null || $this -> getValueOut() -> count() == 0) && !$this -> value_updated){

			$this -> value_updated = true;
			$this -> setValueByValueRaw(null);
		
		}


		return $this -> getValueOut();
	}

	/**
	 * Add the field to query to add an model
	 *
	 * @param Repository $repository
	 *
	 * @return Repository
	 */
	public function addRepository($repository){
		return $repository;
	}

	/**
	 * Add the field to query to edit an model
	 *
	 * @param Repository $repository
	 *
	 * @return Repository
	 */
	public function editRepository($repository){
		return $repository;
	}

	/**
	 * Get the value used in array
	 *
	 * When ORM\Model::toArray is called, this return the value of field
	 *
	 * @return mixed
	 */
	public function getValueToArray(){
		return array_map(function($model){
			return $model -> getPrimaryField() -> getValue();
		},(array)$this -> getValue());
	}
}
?>