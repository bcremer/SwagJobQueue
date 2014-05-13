<?php
namespace ShopwarePlugins\SwagJobQueue\JobQueue;

use Symfony\Component\Console\Output\NullOutput;

class InMemoryQueue implements Queue
{
    /**
     * @var Worker[]
     */
    private $workers;

    /**
     * @param $workers
     */
    public function __construct($workers)
    {
        $this->workers = $workers;
    }

    /**
     * @param Job $job
     */
    public function addJob(Job $job)
    {
        foreach ($this->workers as $worker) {
            if ($worker->canHandle($job)) {
                $output = new NullOutput();
                $worker->handle($job, $output);
            }
        }
    }
}
