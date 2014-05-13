<?php
namespace ShopwarePlugins\SwagJobQueue\JobQueue;

use Symfony\Component\Console\Output\OutputInterface;

class QueueDaemon
{
    /**
     * @var \Pheanstalk_PheanstalkInterface
     */
    private $pheanstalk;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Worker[]
     */
    private $worker;

    /**
     * @param \Pheanstalk_PheanstalkInterface $pheanstalk
     * @param Worker[]                        $worker
     * @param OutputInterface                 $output
     */
    public function __construct(\Pheanstalk_PheanstalkInterface $pheanstalk,  $worker, OutputInterface $output = null)
    {
        $this->pheanstalk = $pheanstalk;
        $this->output = $output;
        $this->worker = $worker;
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->pheanstalk->getConnection()->isServiceListening()) {
            throw new \Exception("Could not connect");
        }

        $this->pheanstalk->watch('shopware');
        $this->pheanstalk->ignore(\Pheanstalk_PheanstalkInterface::DEFAULT_TUBE);

        $this->runLoop();
    }

    /**
     * @return boolean
     */
    protected function runLoop()
    {
        while (true) {
            $this->output->writeln("");
            $this->output->writeln("Waiting for jobs...");
            $job = $this->pheanstalk->reserve();
            $this->output->writeln(sprintf("Received job %s.", $job->getId()));
            if ($this->runJob($job)) {
                $this->output->writeln(sprintf("Delete %s.", $job->getId()));
                $this->pheanstalk->delete($job);
            } else {
                // no handler exists, re-queue for in 60 seconds, maybe restart happend.
                $this->output->writeln(sprintf("Release %s.", $job->getId()));
                $this->pheanstalk->release($job, 100, 60);
            }
        }
    }

    /**
     * @param \Pheanstalk_Job $job
     *
     * @return bool
     */
    private function runJob(\Pheanstalk_Job $job)
    {
        $job = unserialize($job->getData());
        if ($job instanceof Job) {
            $this->output->writeln("Got job named " . $job->getName());

            foreach ($this->worker as $worker) {
                if (!$worker->canHandle($job)) {
                    continue;
                }

                $worker->handle($job, $this->output);

                return true;
            }
        }

        $this->output->writeln("Unknown job payload");

        return false;
    }
}
