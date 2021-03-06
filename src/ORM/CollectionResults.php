<?php

namespace CoreWine\DataBase\ORM;

use CoreWine\Component\Collection;

class CollectionResults extends Collection{

	/**
	 * Repository
	 *
	 * @var ORM\Repository
	 */
	protected $repository;

    /**
     * Convert collection into array
     *
     * @return array
     */
    public function toArray(){
        $return = [];
        foreach($this -> items as $item){
            if($item instanceof Model){
                $return[] = $item -> toArray();
            }else{
                $return[] = $item;
            }
        }

        return $return;
    }

	/**
	 * Set repository
	 *
	 * @param ORM\Repository $repository
	 */
    public function setRepository($repository){
    	$this -> repository = $repository;
    }

	/**
	 * Get repository
	 *
	 * @return ORM\Repository
	 */
    public function getRepository(){
    	return $this -> repository;
    }

    /**
     * Get Pagination
     *
     * @return ORM\Pagination
     */
    public function getPagination(){
    	return $this -> getRepository() -> getPagination();
    }

    public function select($field_name){
        return $this -> map(function($val) use($field_name){
            return is_object($val) ? $val -> $field_name : $val[$field_name];
        });
    }

}