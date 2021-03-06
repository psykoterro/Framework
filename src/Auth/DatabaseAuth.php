<?php
/**
 * Created by PhpStorm.
 * User: fdurano
 * Date: 19/06/18
 * Time: 06:56
 */

namespace App\Auth;

use App\Framework\Auth;
use App\Framework\Auth\User;
use App\Framework\Database\NoRecordException;
use App\Framework\Session\SessionInterface;

/**
 * Class DatabaseAuth
 * @package App\Auth
 */
class DatabaseAuth implements Auth
{

    /**
     *
     * @var UserTable
     */
    private $userTable;

    /**
     *
     * @var SessionInterface
     */
    private $session;

    /**
     *
     * @var User
     */
    private $user;


    /**
     * DatabaseAuth constructor.
     * @param UserTable $userTable
     * @param SessionInterface $session
     */
    public function __construct(UserTable $userTable, SessionInterface $session)
    {
        $this->userTable = $userTable;
        $this->session   = $session;
    }//end __construct()


    /**
     * @param string $username
     * @param string $password
     * @return User|null
     * @throws NoRecordException
     */
    public function login(string $username, string $password): ?User
    {
        if (empty($username) || empty($password)) {
            return null;
        }

        /*
            @var \App\Auth\User $user
        */
        $user = $this->userTable->findBy('username', $username);
        if ($user && password_verify($password, $user->password)) {
            $this->setUser($user);
            return $user;
        }

        return null;
    }//end login()

    /**
     *
     */
    public function logout(): void
    {
        $this->session->delete('auth.user');
    }//end logout()

    /**
     *
     * @return User|null
     * @throws \App\Framework\Database\NoRecordException
     */
    public function getUser(): ?User
    {
        if ($this->user) {
            return $this->user;
        }
        $userId = $this->session->get('auth.user');
        if ($userId) {
            try {
                $this->user = $this->userTable->find($userId);
                return $this->user;
            } catch (NoRecordException $exception) {
                $this->session->delete('auth.user');
                return null;
            }
        }
        return null;
    }//end getUser()

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->session->set('auth.user', $user->id);
        $this->user = $user;
    }//end setUser()
}//end class
