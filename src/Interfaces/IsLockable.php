<?php
/**
 * Interface for all lockable classes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Interfaces;

use Laramore\Exceptions\LockException;

interface IsLockable
{
    /**
     * Disallow any modifications after locking the instance.
     *
     * @return self
     */
    public function lock();

    /**
     * Indicate if the instance is locked or not.
     *
     * @return boolean
     */
    public function isLocked(): bool;

    /**
     * Throw an exception if the instance is unlocked.
     *
     * @param string|null $lockedElement
     * @return self
     * @throws LockException If the instance is unlocked.
     */
    public function needsToBeLocked(string $lockedElement=null);

    /**
     * Throw an exception if the instance is locked.
     *
     * @param string|null $lockedElement
     * @return self
     * @throws LockException If the instance is locked.
     */
    public function needsToBeUnlocked(string $lockedElement=null);
}
