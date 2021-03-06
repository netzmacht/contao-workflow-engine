<?php

namespace Workflow\Event;

use Symfony\Component\EventDispatcher\Event;

use Workflow\Model\ModelInterface;
use Workflow\Flow\Step;
use Workflow\Validation\ViolationList;
use Workflow\Validation\Violation;

/**
 * Validate step access event.
 *
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 * @author Gilles Gauthier <g.gauthier@lexik.fr>
 */
class ValidateStepEvent extends Event
{
    /**
     * @var Step
     */
    private $step;


    /**
     * @var ModelInterface
     */
    private $model;


    /**
     * @var ViolationList
     */
    private $violationList;


    /**
     * Constructor.
     *
     * @param Step           $step
     * @param ModelInterface $model
     * @param ViolationList  $violationList
     */
    public function __construct(Step $step, ModelInterface $model, ViolationList $violationList)
    {
        $this->step          = $step;
        $this->model         = $model;
        $this->violationList = $violationList;
    }


    /**
     * Returns the reached step.
     *
     * @return Step
     */
    public function getStep()
    {
        return $this->step;
    }


    /**
     * Returns the model.
     *
     * @return ModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }


    /**
     * Returns the violation list.
     *
     * @return ViolationList
     */
    public function getViolationList()
    {
        return $this->violationList;
    }


    /**
     * Proxy method to add a violation.
     *
     * @param $message
     */
    public function addViolation($message)
    {
        $this->violationList->add(new Violation($message));
    }

}
