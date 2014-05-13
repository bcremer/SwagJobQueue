<?php
namespace ShopwarePlugins\SwagJobQueue\Commands;

use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Commands\ShopwareCommand;
use ShopwarePlugins\SwagJobQueue\Console\Output\PrefixOutput;
use ShopwarePlugins\SwagJobQueue\JobQueue\QueueDaemon;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunWorkerCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('swagjobqueue:run:worker');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new PrefixOutput($output, getmypid() . ' | ');
        $output->writeLn("Starting worker...");

        /** @var \Enlight_Event_EventManager $eventDispatcher */
        $eventDispatcher = $this->container->get('events');

        $collection = new ArrayCollection();
        $eventDispatcher->collect('SwagJobQueue_Add_Worker', $collection);
        $workers = $collection->getValues();

        if (empty($workers)) {
            $output->writeln("No workers found. Quiting");

            return 1;
        }

        foreach ($workers as $worker) {
            $output->writeln(sprintf("Registering worker: %s", get_class($worker)));
        }

        $pheanstalk = $this->container->get('SwagJobQueue_Pheanstalk_Connection');
        $queue = new QueueDaemon($pheanstalk, $workers, $output);

        $queue->run();
    }
}
