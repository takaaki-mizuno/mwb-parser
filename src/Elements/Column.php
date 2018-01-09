<?php
namespace TakaakiMizuno\MWBParser\Elements;

class Column extends Base
{
    /*
      <value type="object" struct-name="db.mysql.Column" id="A07FB6A1-A013-4BE7-B832-0BC9DD18E645" struct-checksum="0xba88e21c">
      <value type="int" key="autoIncrement">0</value>
      <value type="string" key="expression"></value>
      <value type="int" key="generated">0</value>
      <value type="string" key="generatedStorage"></value>
      <value type="string" key="characterSetName"></value>
      <value _ptr_="0x6080008bd640" type="list" content-type="object" content-struct-name="db.CheckConstraint" key="checks"/>
      <value type="string" key="collationName"></value>
      <value type="string" key="datatypeExplicitParams"></value>
      <value type="string" key="defaultValue"></value>
      <value type="int" key="defaultValueIsNull">0</value>
      <value _ptr_="0x6080008bd1c0" type="list" content-type="string" key="flags"/>
      <value type="int" key="isNotNull">1</value>
      <value type="int" key="length">-1</value>
      <value type="int" key="precision">20</value>
      <value type="int" key="scale">-1</value>
      <link type="object" struct-name="db.SimpleDatatype" key="simpleType">com.mysql.rdbms.mysql.datatype.bigint</link>
      <value type="string" key="comment"></value>
      <value type="string" key="name">id</value>
      <value type="string" key="oldName"></value>
      <link type="object" struct-name="GrtObject" key="owner">28BE9E92-7A45-4761-883C-C58397C635A5</link>
     </value>
     */
    /** @var array */
    protected $attributes;

    protected $id;

    protected $name;

    protected $nullable;

    protected $defaultValue;

    protected $autoIncrement;

    protected $type;

    protected $length;

    protected $precision;

    protected $scale;

    public function parse()
    {
        foreach ($this->object->value as $value) {
            $this->id = (string) $this->object['id'];
            if (in_array((string) $value['type'], ['int', 'string'])) {
                $this->attributes[(string) $value['key']] = (string) $value;
            }
        }

        $this->parseSpecificAttributes();
        $this->parseType();
    }

    protected function parseSpecificAttributes()
    {
        $this->name          = $this->getValue('name');
        $this->nullable      = !$this->getValue('isNotNull');
        $this->defaultValue  = $this->getValue('defaultValue');
        $this->autoIncrement = (bool) $this->getValue('autoIncrement');
        $this->length        = (int) $this->getValue('length');
        $this->precision     = (int) $this->getValue('length');
        $this->precision     = (int) $this->getValue('scale');
    }

    protected function parseType()
    {
        $typeLink   = (string) $this->getLink('simpleType');
        $elements   = explode('.', $typeLink);
        $this->type = $elements[count($elements) - 1];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }
}
