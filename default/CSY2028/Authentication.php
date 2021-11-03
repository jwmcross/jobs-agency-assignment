<?php
namespace Core;

class Authentication {
    private $users;
    private $usernameColumn;
    private $passwordColumn;

    public function __construct(DatabaseTable $users, $usernameColumn, $passwordColumn)
    {
        session_start();
        $this->users = $users;
        $this->usernameColumn = $usernameColumn;
        $this->passwordColumn = $passwordColumn;

    }

    public function login($username,$password)
    {
        $user = $this->users->find($this->usernameColumn, strtolower($username))[0] ?? null;
        //If user not found return a failed login
        if($user==null)
            return false;

        //Checks to see if the account is inactive.
        if($user->status == 0)
            return false;

        //User is found. Confirm the password matches
        if (isset($user) && password_verify($password, $user->password)) {
            session_regenerate_id();
            $_SESSION['username'] = $user->username;
            $_SESSION['userId'] = $user->id;
            $_SESSION['loggedin'] = true;
            return true;
        } else {
            return false;
        }
    }

    public function isLoggedIn()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            return true;
        } else {
            return false;
        }

    }

    //Get the currently logged in user
    public function getUser()
    {
        if ($this->isLoggedIn()) {
            return $this->users->find($this->usernameColumn, strtolower($_SESSION['username']))[0];
        } else {
            return false;
        }
    }


}