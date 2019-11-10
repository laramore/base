<?php
/**
 * Interface for all configurable classes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Interfaces;

interface IsConfigurable
{
    /**
     * Return the configuration path for this field.
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigPath(string $path=null);

    /**
     * Return the configuration for this field.
     *
     * @param string $path
     * @param mixed  $default
     * @return mixed
     */
    public function getConfig(string $path=null, $default=null);
}
