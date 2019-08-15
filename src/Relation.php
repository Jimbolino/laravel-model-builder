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
    protected $junctionFields;

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
    public function __construct($type, $remoteField, $remoteTable, $localField, $prefix, $namespace, $junctionTable = '', $junctionFields = [])
    {
        $this->type = $type;
        $this->remoteField = $remoteField;
        $this->localField = $localField;
        $this->remoteFunction = StringUtils::underscoresToCamelCase(StringUtils::removePrefix($remoteTable, $prefix));
        $this->remoteClass = StringUtils::prettifyTableName($remoteTable, $prefix);
        if (!empty($namespace)) {
            $this->remoteClass = $namespace.'\\'.$this->remoteClass;
        }
        $this->junctionTable = StringUtils::removePrefix($junctionTable, $prefix);
        $this->junctionFields = $junctionFields;

        if ($this->type == 'belongsToMany') {
            $this->remoteFunction = StringUtils::safePlural($this->remoteFunction);
            // many to many has reversed fields
            $this->remoteField = $localField;
            $this->localField = $remoteField;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string = TAB.'public function '.$this->remoteFunction.'()'.LF;
        $string .= TAB.'{'.LF;
        $string .= TAB.TAB.'return $this->'.$this->type.'(';
        $string .= StringUtils::singleQuote($this->remoteClass);

        $withPivot = '';
        if ($this->type == 'belongsToMany') {
            $string .= ', '.StringUtils::export($this->junctionTable);
            if ($this->junctionFields) {
                $withPivot = LF;
                $withPivot .= TAB.TAB.TAB.'->withPivot('.StringUtils::exportIndexedArray($this->junctionFields).')';
            }
        }
        $string .= ', '.StringUtils::export($this->remoteField);
        $string .= ', '.StringUtils::export($this->localField);
        $string .= ')'.$withPivot.';'.LF;
        $string .= TAB.'}'.LF.LF;

        return $string;
    }
}
