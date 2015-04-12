<?php namespace Jimbolino\Laravel\ModelBuilder;

/**
 * Relations, manages all relations of one table
 *
 * User: Jim
 * Date: 5-4-2015
 * Time: 05:40
 */
class Relations {

    // input
    private $localTable = '';
    private $foreignKeys = array();
    private $describes = array();
    private $foreignKeysByTable = array();

    // temporary
    private $manyToMany = array();

    // result
    private $relations = array();


    /**
     * This is where the magic happens
     * @param $localTable
     * @param $foreignKeys
     * @param $describes
     */
    public function __construct($localTable, $foreignKeys, $describes, $foreignKeysByTable) {
        // save
        $this->localTable = $localTable;
        $this->foreignKeys = $foreignKeys;
        $this->describes = $describes;
        $this->foreignKeysByTable = $foreignKeysByTable;

        // do local keys
        foreach($foreignKeys['local'] as $foreignKey) {
            $remoteFunction = StringUtils::underscoresToCamelCase($foreignKey->REFERENCED_TABLE_NAME);
            $type = $this->findType($foreignKey,false);
            $remoteClass = StringUtils::prettifyTableName($foreignKey->REFERENCED_TABLE_NAME);
            $remoteField = $foreignKey->REFERENCED_COLUMN_NAME;
            $remoteTable = $foreignKey->REFERENCED_TABLE_NAME;
            $localField = $foreignKey->COLUMN_NAME;
            $this->relations[] = new Relation($remoteFunction, $type, $remoteClass, $remoteField, $remoteTable, $localField);
        }

        // do remote keys
        foreach($foreignKeys['remote'] as $foreignKey) {
            $remoteFunction = StringUtils::underscoresToCamelCase($foreignKey->TABLE_NAME);
            $type = $this->findType($foreignKey,true);
            if($type == 'belongsToMany') {
                $this->manyToMany[] = $foreignKey;
                continue;
            }
            $remoteClass = StringUtils::prettifyTableName($foreignKey->TABLE_NAME);
            $remoteField = $foreignKey->COLUMN_NAME;
            $remoteTable = $foreignKey->TABLE_NAME;
            $localField = $foreignKey->REFERENCED_COLUMN_NAME;
            $this->relations[] = new Relation($remoteFunction, $type, $remoteClass, $remoteField, $remoteTable, $localField);
        }

        // many to many last
        foreach($this->manyToMany as $foreignKey) {
            $fields = $this->describes[$foreignKey->TABLE_NAME];
            $relations = $this->foreignKeysByTable[$foreignKey->TABLE_NAME];
            foreach($fields as $field) {
                if($field->Key == 'PRI') {
                    if($field->Field == $foreignKey->COLUMN_NAME) {
                        $localField = $field->Field;
                    }
                    else {
                        $remoteField = $field->Field;
                        foreach($relations as $relation) {
                            if($relation->REFERENCED_TABLE_NAME !== $this->localTable) {
                                $remoteTable = $relation->REFERENCED_TABLE_NAME;
                            }
                        }
                    }
                }
            }
            $remoteFunction = StringUtils::underscoresToCamelCase($remoteTable).'s';
            $type = $this->findType($foreignKey,true);
            $remoteClass = StringUtils::prettifyTableName($remoteTable);
            $junctionTable = $foreignKey->TABLE_NAME;
            $this->relations[] = new Relation($remoteFunction, $type, $remoteClass, $remoteField, $remoteTable, $localField, $junctionTable);
        }

    }

    /**
     * Try to determine the type of the relation
     * @param $foreignKey
     * @param $remote
     * @return string
     */
    protected function findType($foreignKey,$remote) {
        if($remote) {
            if($this->isBelongsToMany($foreignKey)) {
                return 'belongsToMany';
            }
            if($this->isHasOne($foreignKey)) {
                return 'hasOne';
            }
            if($this->isHasMany($foreignKey)) {
                return 'hasMany';
            }
        }
        else {
            if($this->isBelongsTo($foreignKey)) {
                return 'belongsTo';
            }
        }
    }

    // One to one: The relationship is from a primary key to another primary key
    protected function isHasOne($foreignKey) {
        $remote = $this->describes[$foreignKey->TABLE_NAME];
        foreach($remote as $field) {
            if($field->Key == 'PRI') {
                if($field->Field == $foreignKey->COLUMN_NAME) {
                    return true;
                }
            }
        }
    }

    // One to many: The rest
    protected function isHasMany($foreignKey) {
        return true;
    }

    // One to one: The relationship is from a primary key to another primary key
    protected function isBelongsTo($foreignKey) {
        return true;
    }

    // Many to many
    protected function isBelongsToMany($foreignKey) {
        $remote = $this->describes[$foreignKey->TABLE_NAME];
        $count = 0;
        foreach($remote as $field) {
            if($field->Key == 'PRI') {
                $count++;
            }
        }
        if($count == 2)  {
            return true;
        }
    }


    /**
     * Outputs all relations to a string
     * @return string
     */
    public function __toString() {
        $res = '';
        foreach($this->relations as $relation) {
            $res .= $relation->__toString();
        }
        return $res;
    }



}
