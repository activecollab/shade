<?php

  namespace ActiveCollab\Shade;

  use Closure, ArrayIterator, IteratorAggregate, ArrayAccess, Countable, JsonSerializable, NotImplementedError, InvalidInstanceError;

  /**
   * Collection of named data
   *
   * @package angie.library
   */
  class NamedList implements IteratorAggregate, ArrayAccess, Countable, JsonSerializable
  {
    /**
     * List data
     *
     * @var array
     */
    protected $data = [];

    /**
     * All data only to be appended to the list
     *
     * @var boolean
     */
    protected $append_only = false;

    /**
     * Set to true if prepareItem() function needs to be called when item value
     * is being set (false by defualt)
     *
     * @var boolean
     */
    protected $prepare_items = false;

    /**
     * Construct named list
     *
     * @param array $data
     */
    public function __construct($data = null)
    {
      if ($data !== null && is_foreachable($data)) {
        foreach ($data as $k => $v) {
          $this->add($k, $v);
        }
      }
    }

    // ---------------------------------------------------
    //  Public interface
    // ---------------------------------------------------

    /**
     * Return true if $name entry exists in this list
     *
     * @param  string  $name
     * @return boolean
     */
    public function exists($name)
    {
      return isset($this->data[$name]);
    }

    /**
     * Return item with $name
     *
     * @param  string $name
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Add data to the list
     *
     * $name can be string in which case system sets $data as value. If $name is
     * array, system will add multiple values, where name is key and value is
     * value of given element
     *
     * @param  string  $name
     * @param  mixed   $data
     * @param  boolean $skip_existing
     * @return mixed
     */
    public function add($name, $data = null, $skip_existing = false)
    {
      if (is_array($name)) {
        foreach ($name as $k => $v) {
          if ($skip_existing && isset($this->data[$k])) {
            continue;
          }

          $this->doAdd($k, $v);
        }

        return $name;
      } else {
        if (isset($this->data[$name])) {
          return $skip_existing ? $this->data[$name] : $this->doAdd($name, $data);
        } else {
          return $this->doAdd($name, $data);
        }
      }
    }

    /**
     * Add data to the beginning of the list
     *
     * @param  string              $name
     * @param  mixed               $data
     * @param  boolean             $skip_existing
     * @return mixed
     * @throws NotImplementedError
     */
    public function beginWith($name, $data, $skip_existing = true)
    {
      if ($this->append_only) {
        throw new NotImplementedError(__METHOD__);
      }

      if ($skip_existing && isset($this->data[$name])) {
        return $this->data[$name];
      }

      return $this->doAdd($name, $data, [ 'begin_with' => true ]);
    }

    /**
     * Add data before $before element
     *
     * @param  string              $name
     * @param  mixed               $data
     * @param  string              $before
     * @param  boolean             $skip_existing
     * @return mixed
     * @throws NotImplementedError
     */
    public function addBefore($name, $data, $before, $skip_existing = false)
    {
      if ($this->append_only) {
        throw new NotImplementedError(__METHOD__);
      }

      if ($skip_existing && isset($this->data[$name])) {
        return $this->data[$name];
      }

      return $this->doAdd($name, $data, [ 'before' => $before ]);
    }

    /**
     * Add item after $after list element
     *
     * @param  string              $name
     * @param  mixed               $data
     * @param  string              $after
     * @param  boolean             $skip_existing
     * @return mixed
     * @throws NotImplementedError
     */
    public function addAfter($name, $data, $after, $skip_existing = false)
    {
      if ($this->append_only) {
        throw new NotImplementedError(__METHOD__);
      }

      if ($skip_existing && isset($this->data[$name])) {
        return $this->data[$name];
      }

      return $this->doAdd($name, $data, [ 'after' => $after ]);
    }

    /**
     * Remove data from the list
     *
     * @param string $name
     */
    public function remove($name)
    {
      if (is_array($name)) {
        foreach ($name as $k) {
          if (isset($this->data[$k])) {
            unset($this->data[$k]);
          }
        }
      } else {
        if (isset($this->data[$name])) {
          unset($this->data[$name]);
        }
      }
    }

    /**
     * Clear the list
     */
    public function clear()
    {
      $this->data = [];
    }

    /**
     * Return all data keys
     *
     * @return array
     */
    public function keys()
    {
      return array_keys($this->data);
    }

    /**
     * return named list as array
     *
     * @return array
     */
    public function toArray()
    {
      return $this->data;
    }

    /**
     * Sort with a callback
     *
     * @param  Closure              $callback
     * @throws InvalidInstanceError
     */
    public function sort($callback)
    {
      if ($callback instanceof Closure) {
        uasort($this->data, $callback);
      } else {
        throw new InvalidInstanceError('callback', $callback, 'Closure');
      }
    }

    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------

    /**
     * Do add item to the list
     *
     * @param  string $name
     * @param  mixed  $data
     * @param  mixed  $options
     * @return mixed
     */
    protected function doAdd($name, $data, $options = null)
    {
      // Add data to the beginning of the list
      if ($options && isset($options['begin_with'])) {
        $new_data = [ $name => ($this->prepare_items ? $this->prepareItem($data) : $data) ];

        foreach ($this->data as $k => $v) {
          $new_data[$k] = $v;
        }

        $this->data = $new_data;

      // Add data before given item
      } elseif ($options && isset($options['before'])) {

        $new_data = [];
        $added = false;

        foreach ($this->data as $k => $v) {
          if ($k == $options['before']) {
            $new_data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
            $added = true;
          }

          $new_data[$k] = $v;
        }

        if (!$added) {
          $new_data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
        }

        $this->data = $new_data;

      // Add after given item
      } elseif ($options && isset($options['after'])) {

        $new_data = [];
        $added = false;

        foreach ($this->data as $k => $v) {
          $new_data[$k] = $v;

          if ($k == $options['after']) {
            $new_data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
            $added = true;
          }
        }

        if (!$added) {
          $new_data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
        }

        $this->data = $new_data;

      // Append
      } else {
        $this->data[$name] = $this->prepare_items ? $this->prepareItem($data) : $data;
      }

      return $this->data[$name];
    }

    /**
     * Prepare item value
     *
     * This function is called for each value when prepare_value flag is set to
     * true for this particular list
     *
     * @param  mixed $value
     * @return mixed
     */
    protected function prepareItem($value)
    {
      return $value;
    }

    // ---------------------------------------------------
    //  Array access
    // ---------------------------------------------------

    /**
     * Check if $offset exists
     *
     * @param  string  $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
      return isset($this->data[$offset]);
    }

    /**
     * Return value at $offset
     *
     * @param  string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
      return $this->data[$offset];
    }

    /**
 	   * Set value at $offset
 	   *
 	   * @param string $offset
 	   * @param mixed $value
 	   */
    public function offsetSet($offset, $value)
    {
      $this->data[$offset] = $value;
    }

    /**
 	   * Unset value at $offset
 	   *
 	   * @param string $offset
 	   */
    public function offsetUnset($offset)
    {
      unset($this->data[$offset]);
    }

    /**
 	   * Number of elements
 	   *
 	   * @return integer
 	   */
    public function count()
    {
      return count($this->data);
    }

    /**
     * Returns an iterator for for this object, for use with foreach
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
      return new ArrayIterator($this->data);
    }

    /**
     * Return serialized data
     *
     * @return array
     */
    public function jsonSerialize()
    {
      return $this->data;
    }

  }
