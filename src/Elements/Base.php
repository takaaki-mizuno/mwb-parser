<?php
namespace TakaakiMizuno\MWBParser\Elements;

abstract class Base
{
    /** @var \SimpleXMLElement $object */
    protected $object;

    /**
     * Base constructor.
     *
     * @param \SimpleXMLElement $object
     */
    public function __construct($object)
    {
        $this->object = $object;
        $this->parse();
    }

    /**
     * @param string $key
     *
     * @return null|string
     */
    public function getValue($key)
    {
        $keys = $this->object->xpath('value[@key="'.$key.'"]');
        if (count($keys) > 0) {
            $value = (string) $keys[0];

            return $value;
        }

        return null;
    }

    public function getLink($key)
    {
        $keys = $this->object->xpath('link[@key="'.$key.'"]');
        if (count($keys) > 0) {
            $value = (string) $keys[0];

            return $value;
        }

        return null;
    }

    abstract public function parse();
}
