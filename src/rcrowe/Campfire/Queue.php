<?php

namespace rcrowe\Campfire;

class Queue implements \ArrayAccess, \Iterator, \Countable
{
    protected $container = array();

    public function add($item)
    {
        if (is_string($item))
        {
            $msg = $item;
        }
        elseif (is_object($item))
        {
            if (!method_exists($item, '__toString'))
            {
                throw new \InvalidArgumentException('Object can not be converted to a string');
            }

            $msg = (string)$item;
        }
        else
        {
            $msg = NULL;
        }

        if (!is_string($msg) OR strlen($msg) === 0)
        {
            throw new \InvalidArgumentException('Can only add a string to the queue');
        }

        if (count($this->container) > 0)
        {
            $keys  = array_keys($this->container);
            $index = ($keys[ count($keys) - 1 ]) + 1;
        }
        else
        {
            $index = 0;
        }

        $this->container[$index] = $msg;

        return $index;
    }

    // public function empty()
    // {
    //     // $this->container = array();
    // }

    public function remove($index = NULL)
    {
        if ($index !== NULL)
        {
            if (!$this->offsetExists($index))
            {
                throw new \OutOfRangeException('Unknown index: '.$index);
            }

            unset($this->container[$index]);
        }
        else
        {
            $this->container = array();
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->add($value);
        }
        else
        {
            $this->remove($offset);
            $this->add($value);
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function rewind()
    {
        reset($this->container);
    }

    public function current()
    {
        return current($this->container);
    }

    public function key()
    {
        return key($this->container);
    }

    public function next()
    {
        return next($this->container);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

    public function count()
    {
        return count($this->container);
    }
}