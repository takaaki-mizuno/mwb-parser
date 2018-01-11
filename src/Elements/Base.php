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
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getValue($key, $default = null)
    {
        $keys = $this->object->xpath('value[@key="'.$key.'"]');
        if (count($keys) > 0) {
            if ($keys[0]['type'] == 'int') {
                $value = (int) $keys[0];
            } else {
                $value = (string) $keys[0];
            }

            return $value;
        }

        return $default;
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

    /**
     * @param array $columns
     *
     * @return mixed
     */
    abstract public function parse();
}
