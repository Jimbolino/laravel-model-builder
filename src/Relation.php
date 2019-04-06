<?php

namespace Jimbolino\Laravel\ModelBuilder;

/**
 * Class Relation, defines one single Relation entry.
 */
class Relation
{
    protected $type;
    protected $remoteField;
    protected $localField;
    protected $remoteFunction;
    protected $remoteClass;
    protected $junctionTable;

    /**
     * Create a relation object.
     *
     * @param $type
     * @param $remoteField
     * @param $remoteTable
     * @param $localField
     * @param $prefix
     * @param $namespace
     * @param string $junctionTable
     */
    public function __construct($type, $remoteField, $remoteTable, $localField, $prefix, $namespace, $junctionTable = '')
    {
        $this->type           = $type;
        $this->remoteField    = $remoteField;
        $this->localField     = $localField;
        $this->remoteFunction = StringUtils::underscoresToCamelCase(StringUtils::removePrefix($remoteTable, $prefix));
        $this->remoteClass    = StringUtils::prettifyTableName($remoteTable, $prefix);
        if (!empty($namespace)) {
            $this->remoteClass = $namespace . '\\' . $this->remoteClass;
        }
        $this->junctionTable = StringUtils::removePrefix($junctionTable, $prefix);

        if ($this->type == 'belongsToMany') {
            $this->remoteFunction = StringUtils::safePlural($this->remoteFunction);
            // many to many has reversed fields
            $this->remoteField = $localField;
            $this->localField  = $remoteField;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string = TAB . '/**' . LF;
        $string .= TAB . ' * Relationship with the ' . class_basename($this->remoteClass) . ' model.' . LF;
        $string .= TAB . ' * ' . LF;
        $string .= TAB . ' * @return  Illuminate\Database\Eloquent\Relations\\' . title_case($this->type) . LF;
        $string .= TAB . ' */ ' . LF;
        $string .= TAB . 'public function ' . $this->remoteFunction . '()' . LF;
        $string .= TAB . '{' . LF;
        $string .= TAB . TAB . 'return $this->' . $this->type . '(';
        $string .= class_basename($this->remoteClass) . '::class';

        if ($this->type == 'belongsToMany') {
            $string .= ', ' . StringUtils::singleQuote($this->junctionTable);
        }

        if ($this->type == 'belongsTo') {
            $string .= ', ' . class_basename($this->remoteClass) . '::FOREIGN_KEY';
        }

        if ($this->type == 'hasMany') {
            $string .= ', self::FOREIGN_KEY';
        }

        // $string .= ', ' . StringUtils::singleQuote($this->remoteField);

        //if(!NamingConvention::primaryKey($this->localField)) {
        // $string .= ', ' . StringUtils::singleQuote($this->localField);
        //}

        $string .= ');' . LF;
        $string .= TAB . '}' . LF . LF;

        return $string;
    }
}
