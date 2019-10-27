<?php
/**
 * This exception indicate that we tried to edit a locked instance.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Exceptions;

use Laramore\Traits\Exception\HasElement;

class LockException extends LaramoreException
{
    use HasElement;
}
