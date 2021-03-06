<?php

namespace Workflow\Flow;

/**
 * Workflow node.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
abstract class Node
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $nextStates;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array  $nextStates
     */
    public function __construct($name, array $nextStates = array())
    {
        $this->name       = $name;
        $this->nextStates = $nextStates;
    }

    /**
     * Return the node name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns all next steps.
     *
     * @return NextStateInterface[]
     */
    public function getNextStates()
    {
        return $this->nextStates;
    }

    /**
     * Returns true if the given step name is one of the next steps.
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasNextState($name)
    {
        return in_array($name, array_keys($this->nextStates));
    }

    /**
     * Returns the target of the given state.
     *
     * @param  string             $name
     * @return NextStateInterface
     */
    public function getNextState($name)
    {
        if ( !$this->hasNextState($name) ) {
            return null;
        }

        return $this->nextStates[$name];
    }

    /**
     * Add a next state.
     *
     * @param string $name
     * @param string $targetType
     * @param Node   $target
     */
    public function addNextState($name, $targetType, Node $target)
    {
        $this->nextStates[$name] = new NextState($name, $targetType, $target);
    }
}
