<?php
namespace ShopwarePlugins\SwagJobQueue\JobQueue;

class BeanstalkQueue implements Queue
{
    /**
     * @var \Pheanstalk_PheanstalkInterface
     */
    private $pheanstalk;

    /**
     * @param \Pheanstalk_PheanstalkInterface $pheanstalk
     */
    public function __construct(\Pheanstalk_PheanstalkInterface $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }

    /**
     * @param Job $job
     */
    public function addJob(Job $job)
    {
        $this->pheanstalk->putInTube('shopware', serialize($job));
    }
}
