<?php
namespace ShopwarePlugins\SwagJobQueue\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrefixOutput implements OutputInterface
{
    /**
     * @var OutputInterface
     */
    private $object;
    private $prefix;

    public function __construct(OutputInterface $output, $prefix)
    {
        $this->object = $output;
        $this->prefix = $prefix;
    }

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param Boolean      $newline  Whether to add a newline
     * @param integer      $type     The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     *
     * @api
     */
    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        return $this->object->write($this->prefix . $messages, $newline, $type);
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param integer      $type     The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     *
     * @api
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        return $this->object->writeln($this->prefix . $messages, $type);
    }

    /**
     * Sets the verbosity of the output.
     *
     * @param integer $level The level of verbosity (one of the VERBOSITY constants)
     *
     * @api
     */
    public function setVerbosity($level)
    {
        return $this->object->setVerbosity($level);
    }

    /**
     * Gets the current verbosity of the output.
     *
     * @return integer The current level of verbosity (one of the VERBOSITY constants)
     *
     * @api
     */
    public function getVerbosity()
    {
        return $this->object->getVerbosity();
    }

    /**
     * Sets the decorated flag.
     *
     * @param Boolean $decorated Whether to decorate the messages
     *
     * @api
     */
    public function setDecorated($decorated)
    {
        return $this->setDecorated($decorated);
    }

    /**
     * Gets the decorated flag.
     *
     * @return Boolean true if the output will decorate messages, false otherwise
     *
     * @api
     */
    public function isDecorated()
    {
        return $this->isDecorated();
    }

    /**
     * Sets output formatter.
     *
     * @param OutputFormatterInterface $formatter
     *
     * @api
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        return $this->setFormatter($formatter);
    }

    /**
     * Returns current output formatter instance.
     *
     * @return OutputFormatterInterface
     *
     * @api
     */
    public function getFormatter()
    {
        return $this->object->getFormatter();
    }
}
