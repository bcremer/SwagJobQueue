# SwagJobQueue

## A job queue integration for shopware

This project provides a shopware plugin that abstracts asynchronous processing via job queues like [beastalk](http://kr.github.io/beanstalkd/).

- Jobs contain the name and the workload of the process to be done.
- The Queue delegates jobs to the workers
- Worker does the processing using the workload from the jobs.

Currently two types of queues are implemented. A InMemoryQueue for testing puposes and a Beanstalk queue.

## Installation

Install dependencies via composer:

```bash
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```

Install Plugin in the shopware plugin manager or via the shopware console:

```bash
$ ./bin/console sw:plugin:refresh
$ ./bin/console sw:plugin:install SwagJobQueue --activate
```

## Run worker loop
The worker loop itself is implemented as a shopware command
```bash
$ ./bin/console swagjobqueue:run:worker
```

For production you should start and monitor the worker process using a proper pocess control system like [Supervisor](http://supervisord.org/).


## Provide own Workers and Jobs

### Define a job
A job expects a `$name` and an `$args` array containing scalar values.

`\ShopwarePlugins\SwagJobQueue\JobQueue\Job::__construct($name, $args = array())`

```php
$job = new \ShopwarePlugins\SwagJobQueue\JobQueue\Job(
    'example_job_name',
    array(
        'foo' => 'bar',
        'baz' => true
    )
);
```

### Put job in queue
The queue can be obtained via the key `SwagJobQueue_Queue` from the di-container.
The interface defines the method `addJob($job)` that can be used to put a job into the queue.

`\ShopwarePlugins\SwagJobQueue\JobQueue\Queue\Queue::addJob($job)`.

```php
/** @var $queue \ShopwarePlugins\SwagJobQueue\JobQueue\Queue */
$queue = $this->container->get('SwagJobQueue_Queue');
$queue->addJob($job);
```


### Worker
The worker has to implement the `ShopwarePlugins\SwagJobQueue\Worker\Worker` interface.


```php
use ShopwarePlugins\SwagJobQueue\JobQueue\Job;
use ShopwarePlugins\SwagJobQueue\JobQueue\Worker;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleWorker implements Worker
{
    public function canHandle(Job $job)
    {
        return $job->getName() === 'example_job_name';
    }

    public function handle(Job $job, OutputInterface $output)
    {
        $args = $job->getArgs();
        // do some work with $args['foo']
    }
}
```

### Register worker
The workers are registered in the queue via the shopware event `SwagJobQueueAddWorker`.

```php
$this->subscribeEvent(
    'SwagJobQueue_Add_Worker',
    'onAddWorker'
);

public function onAddWorker($args)
{
    return ExampleWorker();
}
```

