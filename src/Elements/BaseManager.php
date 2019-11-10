<?php
/**
 * Define a element manager used by Laramore.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Elements;

use Illuminate\Support\{
    Arr, Str
};
use Laramore\Traits\IsLocked;

abstract class BaseManager
{
    use IsLocked;

    /**
     * The element to manage.
     *
     * @var string
     */
    protected $elementClass;

    /**
     * All existing elements.
     *
     * @var array
     */
    protected $elements = [];

    /**
     * All element value names.
     * Examples: migration, factory, admin (for types)
     *
     * @var array
     */
    protected $definitions = [];

    /**
     * Build default elements managed by this manager.
     *
     * @param array $defaults
     */
    public function __construct(array $defaults=[])
    {
        $this->set($defaults);
    }

    /**
     * Indicate if an element exists with the given name.
     *
     * @param  string $name
     * @return boolean
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->elements);
    }

    /**
     * Return the first existant element with the given native value.
     *
     * @param  string $native
     * @return BaseElement
     * @throws \ErrorException If no element exists with this native value.
     */
    public function find(string $native): BaseElement
    {
        foreach ($this->all() as $element) {
            if ($element->native === $native) {
                return $element;
            }
        }

        throw new \ErrorException("The native element {$this->elementClass} [$native] does not exist");
    }

    /**
     * Returns the element with the given name.
     *
     * @param  string $name
     * @return BaseElement
     * @throws \ErrorException If no element exists with this name.
     */
    public function get(string $name): BaseElement
    {
        if ($this->has($name)) {
            return $this->elements[$name];
        }

        throw new \ErrorException("The element {$this->elementClass} [$name] does not exist");
    }

    /**
     * Create a new element with a specific name.
     * Override is allowed, be carefull.
     *
     * @param string $name
     * @param mixed  $native
     * @return BaseElement
     */
    public function create(string $name, $native=null): BaseElement
    {
        $this->needsToBeUnlocked();

        $element = new $this->elementClass($name, $native ?: $name);
        $this->set($element);

        return $element;
    }

    /**
     * Return the element or create one with the given name.
     *
     * @param  string $name
     * @return BaseElement
     */
    public function getOrCreate(string $name): BaseElement
    {
        if ($this->has($name)) {
            return $this->get($name);
        } else {
            return $this->create($name);
        }
    }

    /**
     * Define an element with its name.
     * Override is allowed, be carefull.
     *
     * @param  BaseElement|array $element
     * @return self
     */
    public function set($element): self
    {
        if ($element instanceof $this->elementClass) {
            $this->elements[$name = $element->getName()] = $element;

            foreach ($this->definitions as $keyName => $valueName) {
                if (!$element->has($keyName)) {
                    $element->set($keyName, ($valueName ?? $name));
                }
            }

            return $this;
        }

        if (Arr::isAssoc($element)) {
            foreach ($element as $key => $value) {
                $element = $this->create($key);

                if (\is_array($value)) {
                    foreach ($value as $keyValue => $elementValue) {
                        $element->set($keyValue, $elementValue);
                    }
                } else {
                    $element->native = $value;
                }
            }
        } else {
            foreach ($element as $name) {
                $this->create($name);
            }
        }

        return $this;
    }

    /**
     * Return all possible elements.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->elements;
    }

    /**
     * Count the number of elements.
     *
     * @return integer
     */
    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * Indicate if a value name is defined.
     *
     * @param  string $name
     * @return boolean
     */
    public function doesDefine(string $name): bool
    {
        return isset($this->definitions[$name]);
    }

    /**
     * Add a value name and set the value for this name on each type.
     *
     * @param string $name
     * @return void
     */
    public function define(string $name, $value=null)
    {
        $this->needsToBeUnlocked();

        if (!$this->doesDefine($name)) {
            $this->definitions[$name] = $value;

            foreach ($this->all() as $element) {
                if (!$element->has($name)) {
                    $element->set($name, ($value ?? $element->getName()));
                }
            }
        }
    }

    /**
     * Return the list of value names.
     *
     * @return array
     */
    public function definitions(): array
    {
        return $this->definitions;
    }

    /**
     * Lock every element.
     *
     * @return void
     */
    protected function locking()
    {
        foreach ($this->all() as $element) {
            $element->lock();
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
     * Handle all method calls.
     * Returns the element with the given method name.
     *
     * @param  string $method BaseElement name.
     * @param  array  $args   The first argument could be a value name of the element.
     * @return BaseElement
     */
    public function __call(string $method, array $args): BaseElement
    {
        $method = Str::snake($method);

        if (!$this->has($method) && !$this->isLocked()) {
            $this->create($method);
        }

        $element = $this->get($method);

        if (\count($args) === 0) {
            return $element;
        } else {
            return $element->__invoke(...$args);
        }
    }
}
