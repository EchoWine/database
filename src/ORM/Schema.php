<?php

namespace CoreWine\DataBase\ORM;

use CoreWine\DataBase\ORM\Field\Schema as Field;

class Schema{

	/**
	 * Model
	 */
	public $__model;

	/**
	 * Name of table
	 */
	public $table;

	/**
	 * Primary key
	 */
	public $primary = 'id';

	/**
	 * Field of default sorting
	 */
	public $sortDefaultField = 'id';

	/**
	 * Direction of default sorting
	 */
	public $sortDefaultDirection = 'desc';

	/**
	 * Fields
	 */
	public $fields;


	/**
	 * Construct
	 */
	public function __construct(){
	}

	/**
	 * Get field default for sorting
	 *
	 * @return Field
	 */
	public function getSortDefaultField(){
		return $this -> getField($this -> sortDefaultField);
	}

	/**
	 * Get field direction for sorting
	 *
	 * @return Field
	 */
	public function getSortDefaultDirection(){
		return $this -> sortDefaultDirection;
	}


	/**
	 * Set fields
	 */
	public function fields(){}

	/**
	 * Add field
	 *
	 * @param string $name
	 * @param field $field
	 */
	public function setField($name,$field){
		$this -> fields[$name] = $field;
	}


	/**
	 * Is set a field
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isField($name){
		return isset($this -> fields[$name]);
	}

	/**
	 * Get fields
	 */
	public function getFields(){
		return $this -> fields;
	}

	

	/**
	 * Get a field by column
	 *
	 * @param string $name
	 *
	 * @return Field
	 */
	public function getFieldByColumn($column){
		foreach($this -> getFields() as $field){
			if($field -> getColumn() == $column)
				return $field;
		}

		return null;
	}

	/**
	 * Get all fields and all alias
	 *
	 * @return array of fields
	 */
	public function getFieldsWithAlias(){
		$return = [];
		foreach($this -> getFields() as $field){
			foreach($field -> getAlias() as $alias){
				$return[$alias] = $field;
			}
		}
		return $return;
	}
	
	/**
	 * Get field
	 *
	 * @param string $name
	 *
	 * @return Field
	 */
	public function getField($name){
		return $this -> hasField($name) ? $this -> fields[$name] : null;
	}

	/**
	 * Has fields
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasField($name){
		return isset($this -> fields[$name]);
	}

	/**
	 * Set table
	 */
	public function setTable($table){
		$this -> table = $table;
	}

	/**
	 * Get table
	 */
	public function getTable(){
		return $this -> table;
	}

	/**
	 * Get primary field
	 *
	 * @return ORM\Field\Schema\Field
	 */
	public function getPrimaryField(){
		return $this -> getFieldPrimary();
	}

	/**
	 * Get primary field
	 *
	 * @return ORM\Field\Schema\Field
	 */
	public function getFieldPrimary(){
		foreach($this -> getFields() as $field){
			if($field -> getPrimary())
				return $field;
		}

		return null;
	}

	/**
	 * Get primary field column
	 *
	 * @return string
	 */
	public function getPrimaryColumn(){
		return $this -> getPrimaryField() -> getColumn();
	}


	/**
	 * Return an array with all of schema relation
	 *
	 * @param string $fields
	 *
	 * @return array
	 */
	public function getAllSchemaThroughArray($fields){

		$last_field = $this -> getField($fields[0]);

		if(!$last_field)
			return [];

		$return = [$last_field];
		unset($fields[0]);



		foreach($fields as $field){

			$field = $last_field -> getRelation()::schema() -> getField($field);

			if(!$field)
				return $return;

			$return[] = $field;
			$last_field = $field;
		}

		return $return;
	}
}
?>