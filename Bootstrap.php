<?php
use Doctrine\Common\Collections\ArrayCollection;
use ShopwarePlugins\SwagJobQueue\JobQueue\BeanstalkQueue;
use ShopwarePlugins\SwagJobQueue\JobQueue\InMemoryQueue;

class Shopware_Plugins_Core_SwagJobQueue_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0-dev';
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return array(
            'version'     => $this->getVersion(),
            'autor'       => 'Benjamin Cremer',
            'copyright'   => 'Copyright (c) 2014, Benjamin Cremer',
            'label'       => 'SwagJobQueue',
            'description' => 'Job Queue implementation for shopware',
            'license'     => 'The MIT License (MIT) (http://opensource.org/licenses/MIT)',
            'link'        => 'https://github.com/bcremer/SwagJobQueue',
        );
    }

    /**
     * @return bool
     */
    public function install()
    {
        $this->prepareForm($this->Form());

        $this->subscribeEvent(
            'Shopware_Console_Add_Command',
            'onAddConsoleCommand'
        );

        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_SwagJobQueue_Queue',
            'onInitResourceQueue'
        );

        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_SwagJobQueue_Pheanstalk_Connection',
            'onInitResourcePheanstalkConnection'
        );

        return true;
    }

    /**
     * @param \Shopware\Models\Config\Form $form
     */
    public function prepareForm(Shopware\Models\Config\Form $form)
    {
        $form->setElement('boolean', 'swagjobqueue-inmemory-queue', array(
            'label'    => 'InMemory Queue',
            'required' => true,
            'value'    => false,
        ));

        $form->setElement('text', 'swagjobqueue-beanstalk-host', array(
            'label'    => 'Beanstalk Host',
            'required' => true,
            'value'    => '127.0.0.1',
        ));

        $form->setElement('text', 'swagjobqueue-beanstalk-port', array(
            'label'    => 'Beanstalk Port',
            'required' => true,
            'value'    => '11300',
        ));
    }

    /**
     * @return \Pheanstalk_PheanstalkInterface
     */
    public function onInitResourcePheanstalkConnection()
    {
        $config = $this->Config()->toArray();

        $pheanstalk = new \Pheanstalk_Pheanstalk(
            $config['swagjobqueue-beanstalk-host'],
            $config['swagjobqueue-beanstalk-port']
        );

        return $pheanstalk;
    }

    /**
     * @return \ShopwarePlugins\SwagJobQueue\JobQueue\Queue
     */
    public function onInitResourceQueue()
    {
        $config = $this->Config()->toArray();

        if ($config['swagjobqueue-inmemory-queue']) {
            return $this->createInMemoryQueue();
        }

        $pheanstalk = $this->get('SwagJobQueue_Pheanstalk_Connection');

        return new BeanstalkQueue($pheanstalk);
    }

    /**
     * @return InMemoryQueue
     */
    public function createInMemoryQueue()
    {
        /** @var \Enlight_Event_EventManager $eventDispatcher */
        $eventDispatcher = $this->get('events');

        $collection = new ArrayCollection();
        $eventDispatcher->collect('SwagJobQueue_Add_Worker', $collection);
        $workers = $collection->getValues();

        return new InMemoryQueue($workers);
    }

    /**
     * Register Plugin namespace in autoloader
     */
    public function afterInit()
    {
        require __DIR__.'/vendor/autoload.php';
    }

    /**
     * @param  Enlight_Event_EventArgs $args
     * @return ArrayCollection
     */
    public function onAddConsoleCommand(Enlight_Event_EventArgs $args)
    {
        return new ArrayCollection(array(
            new \ShopwarePlugins\SwagJobQueue\Commands\RunWorkerCommand(),
            new \ShopwarePlugins\SwagJobQueue\Commands\GreetCommand()
        ));
    }
}
