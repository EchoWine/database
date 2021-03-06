<?php

namespace CoreWine\DataBase\Test\Model;

use CoreWine\DataBase\ORM\Model;

class Author extends Model{

    /**
     * Table name
     *
     * @var
     */
    public static $table = 'authors';

    /**
     * Set schema fields
     *
     * @param Schema $schema
     */
    public static function fields($schema){

        $schema -> id();

        $schema -> string('name')
                -> required();

        $schema -> toMany('books',Book::class,'author');
    }
}

?>