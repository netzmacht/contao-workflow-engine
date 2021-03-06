<?php

namespace Workflow\Service;

use DcaTools\DcaTools;
use DcaTools\Definition;


/**
 * Class TableRestrictions
 * @package Workflow\Service
 */
class TableRestrictions extends AbstractService
{

	/**
	 * Service configuration
	 *
	 * @var array
	 */
	protected static $config = array
	(
		'name'      => 'table-restrictions',
		'drivers'   => array('Table'),
		'config'    => array
		(
			'scope'  => array('steps', 'roles'),
			'filter' => array('addFilter'),
			'config' => array('table_restrictions', 'table_operations', 'table_globalOperations'),
		),
	);

	/**
	 * @var array
	 */
	protected $restrictions;


	/**
	 * Initialize the workflow service
	 *
	 * @inheritdoc
	 */
	function initialize()
	{
		if(!$this->applyService() || !$this->applyFilter($this->controller->getCurrentModel()->getEntity()))
		{
			return;
		}

		$definition   = Definition::getDataContainer($this->service->getProperty('tableName'));
		$this->restrictions = deserialize($this->service->getProperty('table_restrictions'), true);

		$definition->set('config/closed', $this->check('closed'));
		$definition->set('config/notDeletable', $this->check('notDeletable'));
		$definition->set('config/notEditable', $this->check('notEditable'));
		$definition->set('config/notSortable', $this->check('notEditable'));

		$operations = deserialize($this->service->getProperty('table_operations'), true);

		foreach($operations as $operation)
		{
			if($definition->hasOperation($operation))
			{
				$definition->getOperation($operation)->remove();
			}
		}

		$operations = deserialize($this->service->getProperty('table_globalOperations'), true);

		foreach($operations as $operation)
		{
			if($definition->hasGlobalOperation($operation))
			{
				$definition->getGlobalOperation($operation)->remove();
			}
		}

		// notSortable is only available in Contao 3.2. Workaround for disabling sorting but keeping order
		// @see https://github.com/contao/core/issues/5254
		if($this->check('notSortable') && $definition->get('list/sorting/fields/0') == 'sorting')
		{
			$sorting    = $definition->get('list/sorting/fields');
			$sorting[0] = 'sorting ';

			$definition->set('list/sorting/fields', $sorting);

			// pass an permission event manually because check permission of DcaTools has already passed
			if($this->service->getProperty('tableName') == \Input::get('table'))
			{
				if($this->controller->getRequestAction() == 'paste') {
					DcaTools::error('Not enough permissions to change sorting');
				}
			}
		}
	}


	/**
	 * @param $restriction
	 * @return bool
	 */
	protected function check($restriction)
	{
		return in_array($restriction, $this->restrictions);
	}


	/**
	 * @return bool
	 */
	protected function applyService()
	{
		$table   = $this->service->getProperty('tableName');
		$process = $this->controller->getProcessHandler($table)->getProcess()->getName();

		$roles   = deserialize($this->service->getProperty('roles'), true);
		$steps   = deserialize($this->service->getProperty('steps'), true);

		if(!$this->controller->getUser()->hasRole($process, $roles))
		{
			return false;
		}

		$state = $this->controller->getCurrentWorkflow()->getProcessHandler($table)->getCurrentState($this->controller->getCurrentModel());

		if($state && !in_array($state->getStepName(), $steps))
		{
			return false;
		}

		return true;
	}

}
