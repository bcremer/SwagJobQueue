<?php
namespace ShopwarePlugins\SwagJobQueue\JobQueue;

interface Queue
{
    /**
     * @param Job $job
     *
     * @return void
     */
    public function addJob(Job $job);
}
