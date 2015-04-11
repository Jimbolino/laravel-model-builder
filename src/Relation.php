<?php namespace App\Http\Controllers\Model;

/**
 * Relation, defines one single Relation entry
 *
 * User: Jim
 * Date: 11-4-2015
 * Time: 00:41
 */
class Relation {

    protected $remoteFunction;
    protected $type;
    protected $remoteClass;
    protected $remoteField;
    protected $remoteTable;
    protected $localField;
    protected $junctionTable;

    public function __construct($remoteFunction, $type, $remoteClass, $remoteField, $remoteTable, $localField, $junctionTable = '') {
        $this->remoteFunction = $remoteFunction;
        $this->type = $type;
        $this->remoteClass = $remoteClass;
        $this->remoteField = $remoteField;
        $this->remoteTable = $remoteTable;
        $this->localField = $localField;
        $this->junctionTable = $junctionTable;
    }

    public function __toString() {
        $string = TAB.'public function '.$this->remoteFunction.'() {'.LF;
        $string .= TAB.TAB.'return $this->'.$this->type.'(';
        $string .= StringUtils::singleQuote($this->remoteClass);

        if($this->type == 'belongsToMany') {
            $string .= ', '.StringUtils::singleQuote($this->junctionTable);
        }

        //if(!NamingConvention::primaryKey($this->localField)) {
            $string .= ', '.StringUtils::singleQuote($this->localField);
        //}

        //if(!NamingConvention::foreignKey($this->remoteField, $this->remoteTable, $this->remoteField)) {
            $string .= ', '.StringUtils::singleQuote($this->remoteField);
        //}

        $string .= ');'.LF;
        $string .= TAB.'}'.LF.LF;
        return $string;
    }
}
