<?php
/**
 * Interface for all provider definding a manager for Laramore.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Interfaces;

interface IsALaramoreProvider
{
    /**
     * Register our facade and create the manager.
     *
     * @return void
     */
    public function register();

    /**
     * Return the generated manager for this provider.
     *
     * @return object
     */
    public static function getManager(): object;
}
