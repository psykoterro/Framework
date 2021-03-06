<?php
/**
 * Created by PhpStorm.
 * User: fdurano
 * Date: 19/06/18
 * Time: 06:22
 */

namespace App\Framework;

use App\Framework\Auth\User;

/**
 * Interface Auth
 * @package App\Framework
 */
interface Auth
{
    /**
     * @return User|null
     */
    public function getUser(): ?User;
}
