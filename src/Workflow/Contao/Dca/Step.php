<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01.11.13
 * Time: 16:07
 */

namespace Workflow\Contao\Dca;

use DcaTools\Translator;

class Step
{
	protected $process;


	public function saveStart($dc)
	{
		// only one field can be defined as start
		if($dc->activeRecord->start)
		{
			\Database::getInstance()
				->prepare('UPDATE tl_workflow_step %s WHERE pid=? AND id!=?')
				->set(array('start' => ''))
				->execute($dc->activeRecord->pid, $dc->activeRecord->id);
		}
	}


	public function generateChildRecord($row)
	{
		$translator = Translator::create('tl_workflow_step');
		$return = $translator->value('name', $row['name']);

		if($row['start'] || $row['end'])
		{

			$return .= ' <span class="tl_gray">[';

			if($row['start'])
			{
				$return .=  $translator->property('start');

				if($row['end'])
				{
					$return .= ', ';
				}
			}

			if($row['end'])
			{
				$return .=  $translator->property('end');
			}

			$return .= ']</span>';
		}

		return $return;
	}


	public function getSteps($dc)
	{
		return $GLOBALS['TL_CONFIG']['workflow_steps'];
	}

	public function getStates($dc)
	{
		return $GLOBALS['TL_CONFIG']['workflow_actions'];
	}


	public function getRoles($dc)
	{
		$this->loadProcess($dc->activeRecord->pid);
		return $this->process->roles;
	}


	public function loadProcess($id)
	{
		if(!$this->process)
		{
			$this->process = \Database::getInstance()
				->prepare('SELECT * FROM tl_workflow_process WHERE id=?')
				->limit(1)
				->execute($id);

			$this->process->roles = trimsplit(',', $this->process->roles);
		}
	}

} 