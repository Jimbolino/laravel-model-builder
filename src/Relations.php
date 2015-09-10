<?php

namespace Jimbolino\Laravel\ModelBuilder;

/**
 * Class Relations, manages all relations of one model.
 */
class Relations
{
    // input
    private $localTable = '';
    private $foreignKeys = [];
    private $describes = [];
    private $foreignKeysByTable = [];
    private $prefix = '';
    private $namespace = '';

    // temporary
    private $manyToMany = [];

    /**
     * @var Relation[]
     */
    private $relations = [];

    /**
     * This is where the magic happens.
     *
     * @param $localTable
     * @param $foreignKeys
     * @param $describes
     * @param $foreignKeysByTable
     * @param $prefix
     * @param $namespace
     */
    public function __construct($localTable, $foreignKeys, $describes, $foreignKeysByTable, $prefix, $namespace)
    {
        // save
        $this->localTable = $localTable;
        $this->foreignKeys = $foreignKeys;
        $this->describes = $describes;
        $this->foreignKeysByTable = $foreignKeysByTable;
        $this->prefix = $prefix;
        $this->namespace = $namespace;

        $remoteField = '';
        $remoteTable = '';
        $localField = '';

        // do local keys
        foreach ($foreignKeys['local'] as $foreignKey) {
            $type = $this->findType($foreignKey, false);
            $remoteField = $foreignKey->COLUMN_NAME;
            $remoteTable = $foreignKey->REFERENCED_TABLE_NAME;
            $localField = $foreignKey->REFERENCED_COLUMN_NAME;
            $this->relations[] = new Relation($type, $remoteField, $remoteTable, $localField, $prefix, $namespace);
        }

        // do remote keys
        foreach ($foreignKeys['remote'] as $foreignKey) {
            $type = $this->findType($foreignKey, true);
            if ($type == 'belongsToMany') {
                $this->manyToMany[] = $foreignKey;
                continue;
            }
            $remoteField = $foreignKey->COLUMN_NAME;
            $remoteTable = $foreignKey->TABLE_NAME;
            $localField = $foreignKey->REFERENCED_COLUMN_NAME;
            $this->relations[] = new Relation($type, $remoteField, $remoteTable, $localField, $prefix, $namespace);
        }

        // many to many last
        foreach ($this->manyToMany as $foreignKey) {
            $fields = $this->describes[$foreignKey->TABLE_NAME];
            $relations = $this->foreignKeysByTable[$foreignKey->TABLE_NAME];
            foreach ($fields as $field) {
                if ($field->Key == 'PRI') {
                    if ($field->Field == $foreignKey->COLUMN_NAME) {
                        $localField = $field->Field;
                    } else {
                        $remoteField = $field->Field;
                        foreach ($relations as $relation) {
                            if ($relation->REFERENCED_TABLE_NAME !== $this->localTable) {
                                $remoteTable = $relation->REFERENCED_TABLE_NAME;
                            }
                        }
                    }
                }
            }
            $type = $this->findType($foreignKey, true);
            $junctionTable = $foreignKey->TABLE_NAME;
            $this->relations[] = new Relation($type, $remoteField, $remoteTable, $localField, $prefix, $namespace, $junctionTable);
        }
    }

    /**
     * Try to determine the type of the relation.
     *
     * @param $foreignKey
     * @param $remote
     *
     * @return string
     */
    protected function findType($foreignKey, $remote)
    {
        if ($remote) {
            if ($this->isBelongsToMany($foreignKey)) {
                return 'belongsToMany';
            }
            if ($this->isHasOne($foreignKey)) {
                return 'hasOne';
            }

            return 'hasMany';
        } else {
            return 'belongsTo';
        }
    }

    /**
     * One to one: The relationship is from a primary key to another primary key.
     *
     * @param $foreignKey
     *
     * @return bool
     */
    protected function isHasOne($foreignKey)
    {
        $remote = $this->describes[$foreignKey->TABLE_NAME];
        foreach ($remote as $field) {
            if ($field->Key == 'PRI') {
                if ($field->Field == $foreignKey->COLUMN_NAME) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Many to many.
     *
     * @param $foreignKey
     *
     * @return bool
     */
    protected function isBelongsToMany($foreignKey)
    {
        $remote = $this->describes[$foreignKey->TABLE_NAME];
        $count = 0;
        foreach ($remote as $field) {
            if ($field->Key == 'PRI') {
                $count++;
            }
        }
        if ($count == 2) {
            return true;
        }

        return false;
    }

    /**
     * Outputs all relations to a string.
     *
     * @return string
     */
    public function __toString()
    {
        $res = '';
        foreach ($this->relations as $relation) {
            $res .= $relation->__toString();
        }

        return $res;
    }
}
