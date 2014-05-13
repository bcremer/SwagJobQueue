<?php
namespace ShopwarePlugins\SwagJobQueue\JobQueue;

use Symfony\Component\Console\Output\OutputInterface;

interface Worker
{
    /**
     * @param Job $job
     *
     * @return bool
     */
    public function canHandle(Job $job);

    /**
     * @param Job             $job
     * @param OutputInterface $output
     */
    public function handle(Job $job, OutputInterface $output);
}
