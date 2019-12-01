<?php
/**
 * Add a lock management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Traits;

use Illuminate\Support\Traits\Macroable;
use Laramore\Exceptions\LockException;

trait HasLockedMacros
{
    use Macroable {
        Macroable::macro as protected macroFromTrait;
    }

    /**
     * Register a custom macro.
     *
     * @param  string   $name
     * @param  callable $macro
     * @return void
     */
    public static function macro($name, callable $macro)
    {
        if (app()->isBooted()) {
            throw new LockException('No more macros could be defined after the application booting', $name);
        }

        static::macroFromTrait($name, $macro);
    }
}