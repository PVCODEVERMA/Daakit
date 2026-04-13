<?php

class myhook
{

    protected $actions;
    protected $filters;

    public function __construct()
    {
        $this->actions = new stdClass;
        $this->filters = new stdClass;
    }

    function add_action($event = NULL, $class = NULL, $method = NULL, $priority = NULL)
    {
        $action = new stdClass;
        $action->class = $class;
        $action->method = $method;
        $action->priority = $priority;
        $this->actions->{$event}[] = $action;
    }

    function do_action($event = NULL, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL)
    {
        if ($event)
            if (isset($this->actions->{$event}) && !empty($this->actions->{$event})) {
                array_multisort(array_column($this->actions->{$event}, 'priority'), SORT_ASC, $this->actions->{$event});
                foreach ($this->actions->{$event} as $hook) {
                    if (is_array($arg1))
                        call_user_func_array([$hook->class, $hook->method], $arg1);
                    else
                        call_user_func([$hook->class, $hook->method], $arg1, $arg2, $arg3, $arg4);
                }
            }
    }

    function add_filter($event = NULL, $class = NULL, $method = NULL, $priority = NULL)
    {
        $filter = new stdClass;
        $filter->class = $class;
        $filter->method = $method;
        $filter->priority = $priority;
        $this->filters->{$event}[] = $filter;
    }

    function apply_filters($event = NULL, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL)
    {
        if ($event)
            if (isset($this->filters->{$event}) && !empty($this->filters->{$event})) {
                array_multisort(array_column($this->filters->{$event}, 'priority'), SORT_ASC, $this->filters->{$event});
                foreach ($this->filters->{$event} as $hook) {
                    if (is_array($arg1))
                        $arg1 = call_user_func([$hook->class, $hook->method], $arg1, $arg2, $arg3, $arg4);
                    else
                        $arg1 = call_user_func([$hook->class, $hook->method], $arg1, $arg2, $arg3, $arg4);
                }
                return $arg1;
            }
        return $arg1;
    }
}

global $myhook;
$myhook = new myhook();

function add_action($event = NULL, $class = NULL, $method = NULL, $priority = 10)
{
    global $myhook;
    $myhook->add_action($event, $class, $method, $priority);
}

function do_action($event = false, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL)
{
    global $myhook;
    $myhook->do_action($event, $arg1, $arg2, $arg3, $arg4);
}

function add_filter($event = NULL, $class = NULL, $method = NULL, $priority = 10)
{
    global $myhook;
    $myhook->add_filter($event, $class, $method, $priority);
}

function apply_filters($event = false, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL)
{
    global $myhook;
    return $myhook->apply_filters($event, $arg1, $arg2, $arg3, $arg4);
}
