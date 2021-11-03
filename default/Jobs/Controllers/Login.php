<?php
namespace Jobs\Controllers;
use Core\Authentication;

class Login
{
    private $authentication;
    private $post;
    private $get;

    public function __construct(Authentication $authentication, array $get=[], array $post=[])
    {
        $this->authentication = $authentication;
        $this->get = $get;
        $this->post = $post;
    }

    public function loginForm($errors = null)
    {
        return [
            'template'=> 'login.html.php',
            'title'=>'Jo\'s Jobs - Login',
            'variables'=>[
                'errors'=>$errors ?? null
            ]
        ];
    }

    //Login after form submitted via POST
    public function loginSubmit()
    {
        //Validate the form for errors
        $errors = $this->validateForm();
        //Return if errors are found
        if(count($errors)>0)
            return $this->loginForm($errors);

        $post = $this->post['user'];

        //Check the details of the login to authenticate login attempt.
        if ($this->authentication->login($post['username'], $post['password']))
        {
            header('location: /login/success');
        } else {
            $error[] = nl2br("Login Failed. \r\n Wrong Username or Password.");
            return $this->loginForm($error);
        }
    }

    //Validation of the login form
    public function validateForm()
    {
        $errors = [];
        $post = $this->post['user'];
        if (empty($post['username'])) $errors[] = 'Empty Username Field';

        if (empty($post['password'])) $errors[] = 'Empty Password Field';

        return $errors;
    }

    public function success()
    {
        return [
            'template'=>'../templates/loginsuccess.html.php',
            'title'=>'Jo\'s Jobs - Login Successful'
        ];

    }

    public function permissionsError()
    {
        return [
            'template'=>'permissionserror.html.php',
            'title'=>'Jo\'s Jobs - Unauthorised Access'
        ];
    }


    public function logout()
    {
        if($this->authentication->isLoggedIn()) {
            unset($_SESSION);
            session_destroy();
            header('location: /');
        } else {
            header('location: /');
        }

    }

}