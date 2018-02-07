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
    protected $attributes = [];

    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $nullable;

    /** @var mixed */
    protected $defaultValue;

    /** @var bool */
    protected $autoIncrement;

    /** @var string */
    protected $type;

    /** @var int */
    protected $length;

    /** @var int */
    protected $precision;

    /** @var int */
    protected $scale;

    /** @var bool */
    protected $unsigned;

    public function parse()
    {
        $this->id = (string) $this->object['id'];

        foreach ($this->object->value as $value) {
            if (in_array((string) $value['type'], ['int', 'string'])) {
                $this->attributes[(string) $value['key']] = (string) $value;
            }
        }

        $this->parseFlags();
        $this->parseSpecificAttributes();
        $this->parseType();
    }

    protected function parseFlags()
    {
        $this->unsigned = false;
        $flags          = $this->object->xpath('value[@key="flags"]');
        foreach ($flags as $flag) {
            $values         = $flag->xpath('value');
            foreach ($values as $value) {
                if ((string) $value == 'UNSIGNED') {
                    $this->unsigned = true;
                }
            }
        }
    }

    protected function parseSpecificAttributes()
    {
        $this->name          = $this->getValue('name');
        $this->nullable      = !$this->getValue('isNotNull');
        $this->defaultValue  = $this->getValue('defaultValue');
        $this->autoIncrement = (bool) $this->getValue('autoIncrement');
        $this->length        = (int) $this->getValue('length');
        $this->precision     = (int) $this->getValue('precision');
        $this->scale         = (int) $this->getValue('scale');
    }

    protected function parseType()
    {
        $typeLink   = (string) $this->getLink('simpleType');
        $elements   = explode('.', $typeLink);
        $this->type = $elements[count($elements) - 1];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * @return int
     */
    public function getScale(): int
    {
        return $this->scale;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }
}
