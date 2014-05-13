<?php
namespace ShopwarePlugins\SwagJobQueue\JobQueue;

class Job
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $args;

    /**
     * @param string $name
     * @param array  $args
     */
    public function __construct($name, $args = array())
    {
        $this->name = $name;
        $this->args = $args;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }
}
