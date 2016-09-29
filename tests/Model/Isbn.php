<?php

namespace CoreWine\DataBase\Test\Model;

use CoreWine\DataBase\ORM\Model;

class Isbn extends Model{

    /**
     * Table name
     *
     * @var
     */
    public static $table = 'isbn';

    /**
     * Set schema fields
     *
     * @param Schema $schema
     */
    public static function setSchemaFields($schema){

        $schema -> string('code') -> primary();

        $schema -> string('type');

    }
}

?>