<?php

namespace Workflow\Controller;

use DcGeneral\Data\ModelInterface as EntityInterface;

/**
 * Interface WorkflowInterface
 * @package Workflow\Workflow
 */
interface WorkflowInterface
{

	/**
	 * Defines if workflow should handle the publishing process
	 */
	const PUBLISH_MODE_UNSUPPORTED = 0;

	const PUBLISH_MODE_DEFAULT = 1;

	const PUBLISH_MODE_INVERTED = 2;

	/**
	 * Are used to decide which data should be stored. DATA_ENTITY and DATA_CHILDREN can be combined
	 */
	const DATA_NONE = 0;

	const DATA_ENTITY = 1;

	const DATA_CHILDREN = 2;


	/**
	 * @param EntityInterface $entity
	 */
	public function __construct(EntityInterface $entity);


	/**
	 * @return mixed
	 */
	public static function getIdentifier();


	/**
	 * Get tables which are supported by the workflow
	 *
	 * @return array
	 */
	public static function getSupportedDataContainers();


	/**
	 * @param EntityInterface $entity
	 * @return mixed
	 */
	public static function isEntitySupported(EntityInterface $entity);


	/**
	 * @param $tableName
	 * @return mixed
	 */
	public static function getConfig($tableName);


	/**
	 * @return mixed
	 */
	public function initialize();


	/**
	 * @return EntityInterface
	 */
	public function getEntity();


	/**
	 *
	 */
	public function getProcessConfiguration();


	/**
	 * Consider whether table has a process or not
	 *
	 * @param string $tableName
	 * @return true
	 */
	public function hasProcess($tableName);


	/**
	 * @param EntityInterface $entity
	 * @return mixed
	 */
	public function isAssigned(EntityInterface $entity);


	/**
	 * @param EntityInterface $entity
	 * @return mixed
	 */
	public function getPriority(EntityInterface $entity);


	/**
	 * @param Controller $controller
	 */
	public function setController(Controller $controller);


	/**
	 * @return Controller
	 */
	public function getController();


	/**
	 * @param $tableName
	 * @return \Workflow\Handler\ProcessHandlerInterface
	 */
	public function getProcessHandler($tableName);


	/**
	 * Get workflow data
	 *
	 * @param EntityInterface $entity
	 * @return mixed
	 */
	public function getWorkflowData(EntityInterface $entity);


	/**
	 * @param EntityInterface $entity
	 * @param $tableName=null
	 * @return EntityInterface|null
	 */
	public function getParent(EntityInterface $entity, $tableName=null);

}