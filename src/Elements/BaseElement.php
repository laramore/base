<?php
/**
 * Define a specific element.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Elements;

use Illuminate\Support\Str;
use Laramore\Exceptions\LockException;
use Laramore\Traits\IsLocked;

abstract class BaseElement
{
    use IsLocked;

    /**
     * The element name.
     *
     * @var string
     */
    protected $name;

    /**
     * All defined values for this element.
     *
     * @var array
     */
    protected $values = [];

    /**
     * Create the element with a specific name and a native element value.
     *
     * @param string $name
     * @param string $native
     */
    public function __construct(string $name, string $native)
    {
        $this->name = $name;
        $this->set('native', $native);
    }

    /**
     * Return the element name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Indicate if the element has a value for a given name.
     *
     * @param  string $key
     * @return boolean
     */
    public function has(string $key): bool
    {
        return \in_array($key, \array_keys($this->values));
    }

    /**
     * Return the element value for a given name.
     *
     * @param  string $key
     * @return mixed
     * @throws \ErrorException If this type has no value for a specific key name.
     */
    public function get(string $key='name')
    {
        if (\method_exists($this, $method = 'get'.Str::studly($key))) {
            return \call_user_func([$this, $method]);
        } else if ($key === 'name') {
            return $this->name;
        } else if ($this->has($key)) {
            return $this->values[$key];
        }

        $class = static::class;

        throw new \ErrorException("The element $class [{$this->getName()}] has no value for the key [$key]");
    }

    /**
     * Set the value for a given name.
     *
     * @param string $key
     * @param mixed  $value
     * @return self
     */
    public function set(string $key, $value): self
    {
        $this->needsToBeUnlocked();
        if (\method_exists($this, $method = 'set'.Str::studly($key))) {
            return \call_user_func([$this, $method], $value);
        }

        $this->values[$key] = $value;

        return $this;
    }

    /**
     * Actions when locking.
     *
     * @return void
     * @throws LockException If this element has no native value.
     */
    protected function locking()
    {
        if (!$this->has('native')) {
            throw new LockException($this, "Need a native element definition for {$this->getName()}", 'native');
        }
    }

    /**
     * Return the value for a given name.
     *
     * @param  string $key
     * @return mixed
     */
    public function __isset(string $key)
    {
        return $this->has($key);
    }

    /**
     * Return the value for a given name.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Set the value for a given name.
     *
     * @param string $key
     * @param mixed  $value
     * @return self
     */
    public function __set(string $key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Return the value for a given name ("get{$name}") or define it.
     * If start with "get" return the value.
     * Else, it calls the value as function.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     * @throws \BadMethodCallException Else.
     */
    public function __call(string $method, array $args)
    {
        if (Str::startsWith($method, 'get')) {
            return $this->get(\substr(Str::snake($method), 4), ...$args);
        } else if (Str::startsWith($method, 'set')) {
            return $this->set(\substr(Str::snake($method), 4), ...$args);
        } else if (Str::startsWith($method, 'has')) {
            return $this->has(\substr(Str::snake($method), 4), ...$args);
        } else {
            return $this->get($method)->__invoke(...$args);
        }
    }

    /**
     * Execute the element
     *
     * @param  mixed $valueName
     * @return mixed
     */
    public function __invoke($valueName=null)
    {
        if ($valueName) {
            return $this->get($valueName);
        } else {
            return $this->__toString();
        }
    }

    /**
     * Return the element native value.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
