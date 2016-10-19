<?php

namespace CoreWine\DataBase\ORM\Field\Relations\ToOne;

use CoreWine\DataBase\ORM\Field\Field\Model as FieldModel;

class Model extends FieldModel{

	/**
	 * Last value relation 
	 *
	 * @var mixed
	 */
	protected $last_value_relation = null;

	/**
	 * Is the value updated
	 *
	 * @var bool
	 */
	public $value_updated = false;

	/**
	 * Initialze the alis
	 */
	public function iniAlias(){
		$this -> alias = [$this -> getSchema() -> getColumn(),$this -> getSchema() -> getName()];
	}

	/**
	 * Set the value raw by repository
	 *
	 * Given $row and $relations this method must set the current value of field
	 *
	 * @param array $row list of all columns value retrieved in previous query
	 * @param bool $persist
	 * @param array $relation list of all results retrieved with previous joins query
	 *
	 * @return mixed
	 */
	public function setValueRawFromRepository($row,$persist = false,$relations = []){
		
		$column = $this -> getSchema() -> getColumn();
		$relation = $this -> getSchema() -> getRelation();
		$relation_column = $this -> getSchema() -> getRelationColumn();

		$value = null;

		$this -> value_raw = null;

		if(isset($row[$column]))
			$this -> value_raw = $row[$column];

		if(isset($relations[$relation])){

			foreach($relations[$relation] as $rel){
				if($rel -> getFieldByColumn($relation_column) -> getValue() == $this -> value_raw){
					$value = $rel;
					break;
				}
			}
		}

		if(!$persist && $value){

			$this -> setValue($this -> parseRawToValue($value),false);
			$this -> persist = $persist;
		}
	}

	/**
	 * Set the value raw
	 *
	 * @return mixed
	 */
	public function setValueRawToRepository($value_raw,$persist = false){

		if(is_object($value_raw)){

			if(get_class($value_raw) !== $this -> getSchema() -> getRelation())
				throw new \Exception("Incorrect object assigned to field");

			if($value_raw)
				$value_raw = $value_raw -> getFieldByColumn($this -> getSchema() -> getRelationColumn()) -> getValue();
		}

		$this -> value_raw = $value_raw;

		if(!$persist){
			$this -> setValue($this -> parseRawToValue($value_raw),false);
			$this -> persist = $persist;
		}
	}


	/**
	 * Get the value raw
	 *
	 * @return mixed
	 */
	public function getValueRaw(){
		
		if(is_object($this -> value_raw)){
			die("CoreWine\DataBase\ORM\Field\Relations\ToOne\Model: This is bad");
		}

		return $this -> value_raw;
	}

	public function setValueRaw($value){
		$this -> value_raw = $value;
	}
	/**
	 * Set the value
	 *
	 * @param mixed $value
	 * @param bool $persist
	 */
	public function setValue($value = null,$persist = true){
		
		if($value)
			$this -> value_updated = true;

		# Improve check correct instance of
		if(!is_object($value)){
			$this -> setValueRawToRepository($value,true);
			$this -> value = null;
			$this -> persist = $persist;
			return;
		}
		
		if(get_class($value) !== $this -> getSchema() -> getRelation()){
			throw new \Exception(basename($this -> getObjectModel() -> getClass()).
				" Incorrect object assigned to field: ". 
				basename($this -> getSchema() -> getRelation())." != ".basename(get_class($value)));
		}

		$this -> value = $value;

		if($value)
			$this -> last_value_relation = $value -> {$this -> getSchema() -> getRelationColumn()};

		if($persist){
			$this -> setValueRawToRepository($this -> parseValueToRaw($value),true);
			$this -> persist = $persist;
		}
	}


	/**
	 * Get the value
	 *
	 * @return mixed
	 */
	public function getValue(){


		if($this -> getLastAliasCalled() == $this -> getSchema() -> getColumn())
			return $this -> getValueRaw();

		
		if(!$this -> value_updated && $this -> getValueRaw()){

			$this -> value = $this -> getSchema() -> getRelation()::where($this -> getSchema() -> getRelationColumn(),$this -> getValueRaw()) -> first();
			$this -> value_updated = true;

		}

		return $this -> value;
	}

	/**
	 * Get the value
	 *
	 * When ORM\Model::toArray is called, this return the value of field
	 *
	 * @return mixed
	 */
	public function getValueToArray(){
		return $this -> getValueRaw();
	}

	/**
	 * Get the name used in array
	 *
	 * When ORM\Model::toArray is called, this return the name of field
	 *
	 * @return string
	 */
	public function getNameToArray(){
		return $this -> getSchema() -> getColumn();
	}

	/**
	 * Check persist
	 *
	 * Re-check the current status of persist. This function is called before save() to check which field put in query
	 */
	public function checkPersist(){

		if(!$this -> getValue())
			return;

		if(!($this -> getValue() instanceof \CoreWine\DataBase\ORM\Model))
			return;

		$field = $this -> getSchema() -> getRelationColumn();
		$current = $this -> getValue() -> $field;


		# The primary key may be changed
		if($current != $this -> last_value_relation){

			# Re-Set value
			$this -> last_alias_called = $this -> getSchema() -> getName();
			$this -> setValue($this -> getValue());
			$this -> persist = true;
		}
	}

}
?>