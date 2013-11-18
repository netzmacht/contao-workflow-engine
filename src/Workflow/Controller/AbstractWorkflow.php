<?php

namespace Workflow\Controller;

use DcaTools\Data\ConfigBuilder;
use DcGeneral\Data\ModelInterface as EntityInterface;
use Workflow\Handler\ProcessFactory;
use Workflow\Handler\ProcessHandler;
use Workflow\Service\ServiceFactory;

abstract class AbstractWorkflow implements WorkflowInterface, GetWorkflowListenerInterface
{

	/**
	 * @var \DcGeneral\Data\ModelInterface
	 */
	protected $workflow;

	/**
	 * @var \Workflow\Controller\Controller
	 */
	protected $controller;

	/**
	 * @var \Workflow\Handler\ProcessHandlerInterface[]
	 */
	protected $handlers = array();

	/**
	 * @var array
	 */
	protected $processes = array();


	/**
	 * Construct
	 *
	 * @param EntityInterface $entity
	 */
	public function __construct(EntityInterface $entity)
	{
		$this->workflow   = $entity;

		$processes = deserialize($this->workflow->getProperty('processes'), true);

		foreach($processes as $process)
		{
			$this->processes[$process['table']] = $process['process'];
		}
	}


	/**
	 * Bootstrap workflow
	 *
	 * @param Controller $controller
	 */
	public static function bootstrap(Controller $controller)
	{
		$eventName = 'workflow.controller.get-workflow-types';
		$listener  = array(get_called_class(), 'listenerGetWorkflowType');

		$controller->getEventDispatcher()->addListener($eventName, $listener);
	}


	/**
	 * Set controller
	 *
	 * @param Controller $controller
	 */
	public function setController(Controller $controller)
	{
		$this->controller = $controller;
	}


	/**
	 * @return \Workflow\Controller\Controller
	 */
	public function getController()
	{
		return $this->controller;
	}


	/**
	 * Get current process handler
	 *
	 * @param $tableName
	 *
	 * @return \Workflow\Handler\ProcessHandlerInterface
	 */
	public function getProcessHandler($tableName)
	{
		if(!isset($this->handlers[$tableName]))
		{
			$process = ProcessFactory::create($this->processes[$tableName]);
			$storage = $GLOBALS['container']['workflow.model-state-storage'];
			$handler = new ProcessHandler($process, $this->controller->getEventDispatcher(), $storage);

			$this->handlers[$tableName] = $handler;
		}

		return $this->handlers[$tableName];
	}


	/**
	 * @param EntityInterface $entity
	 * @return EntityInterface|null
	 */
	public function getParent(EntityInterface $entity)
	{
		$config = $this->getConfig($entity->getProviderName());

		if($config && $config['parent'])
		{
			$driver = $this->controller->getDataProvider($config['parent']);

			return ConfigBuilder::create($driver)
				->filterEquals('id', $entity->getProperty('pid'))
				->fetch();
		}

		return null;
	}


	/**
	 * Initialize workflow services
	 */
	protected function initializeServices()
	{
		$services = ServiceFactory::forWorkflow($this->workflow, $this->controller);

		/** @var \Workflow\Service\ServiceInterface $service */
		foreach($services as $service)
		{
			$service->initialize();
		}
	}

}