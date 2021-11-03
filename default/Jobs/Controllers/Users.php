<?php
namespace Jobs\Controllers;
use \Core\DatabaseTable;
use \Core\Authentication;

Class Users {

    private $usersTable;
    private $authentication;
    private $get;
    private $post;


    public function __construct(DatabaseTable $usersTable, Authentication $authentication,
                                array $get=[], array $post=[])
    {
        $this->usersTable = $usersTable;
        $this->authentication = $authentication;
        $this->get = $get;
        $this->post= $post;
    }

    //Lists all the users
    public function list()
    {
        $users = $this->usersTable->findAll();
        $mainClass = 'sidebar';
        $heading = 'All Users';

        return [
            'template' => 'admin/listusers.html.php',
            'title'=> 'Jo\'s Jobs - Users',
            'mainClass'=> $mainClass,
            'variables' => [
                'users' => $users ?? null,
                'heading'=> $heading
            ]
        ];
    }

    //List the staff account
    public function listStaff()
    {
        $users = $this->usersTable->find('type', 1);
        $mainClass = 'sidebar';
        $heading = 'Staff Users';


        return [
            'template' => 'admin/listusers.html.php',
            'title'=> 'Jo\'s Jobs - Users',
            'mainClass'=> $mainClass,
            'variables' => [
                'users' => $users ?? null,
                'heading'=> $heading
            ]
        ];
    }

    //List the client accounts
    public function listClients()
    {
        $users = $this->usersTable->find('type', 2);
        $mainClass = 'sidebar';
        $heading = 'Client Users';

        return [
            'template' => 'admin/listusers.html.php',
            'title'=> 'Jo\'s Jobs - Users',
            'mainClass'=> $mainClass,
            'variables' => [
                'users' => $users ?? null,
                'heading'=> $heading
            ]
        ];
    }

    //Form for editting a user
    public function edit($errors = null, $id = null)
    {

        if(!isset($this->post['userId'])) header('location: /admin/users/add');

        $mainClass = 'sidebar';
        //Get the user from the database to edit
        if (isset($this->post['userId']))
            $user = $this->usersTable->findById($this->post['userId']);
        else
            //When the form was submitted with errors. This query is used with the local var id
            $user = $this->usersTable->findById($id);

        $title = 'Jo\'s Jobs - Admin - Edit User';
        $heading = 'Edit User';
        $action = 'edit/save';

        $permissions = $this->getPermissionValues();

        return [
            'template' => 'admin/edituser.html.php',
            'title'=> $title,
            'mainClass'=> $mainClass,
            'variables' => [
                'user' => $user ?? null,
                'errors'=> $errors ?? null,
                'permissions'=> $permissions,
                'heading'=> $heading,
                'action'=> $action
            ]
        ];

    }

    //Form validation when editting a user
    public function editValidateForm()
    {
        $userPost = $this->post['user'];
        $errors = [];

        //Check if the username is being changed
        if($this->post['old_username'] !== $userPost['username']) {
            //Check if the new username already exists in the database
            $checkUsername = $this->usersTable->find('username', $userPost['username']);
            if ($checkUsername != null)
                $errors[] = 'Username already exists';
        }

        //Check if the password is being changed.
        //And if the new password matches
        if (!empty($userPost['password']) && !empty($this->post['confirm_password'])) {
            if ($userPost['password'] !== $this->post['confirm_password'])
                $errors[] = 'Mismatched Password';
        } else {
            unset($this->post['user']['password']);
        }

        return $errors;
    }

    //When a form is submitted. For Editting Jobs
    public function editSave()
    {
        //Validate the form for errors
        $errors = $this->editValidateForm();
        $post = $this->post['user'];

        if (count($errors)== 0){
            //Unset the confirm password from the post array
            unset($post['confirm_password']);
            //Add up all the permissions set. To be inputted into the database
            $setPermissions = array_sum($this->post['permissions'] ?? []);
            $post['permissions'] = $setPermissions;
            //If a new password is being inputted. If the password field is not empty.
            //Add the new password to the post array.
            if(!empty($post['password']))
                //Encrypt the password
                $post['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
            $this->usersTable->save($post);
            header('location: /admin/users/list');
        } else {
            return $this->edit($errors, $post['id']);
        }
    }

    //Form for adding a new users
    public function add($errors = null)
    {

        $mainClass = 'sidebar';

        $title = 'Jo\'s Jobs - Admin - Add User';
        $heading = 'Add User';
        $action = 'add';

        $permissions = $this->getPermissionValues();

        return [
            'template' => 'admin/edituser.html.php',
            'title'=> $title,
            'mainClass'=> $mainClass,
            'variables' => [
                'user' => $user ?? null,
                'errors'=> $errors ?? null,
                'permissions'=> $permissions,
                'heading'=> $heading,
                'action'=> $action
            ]
        ];

    }

    //Form validation when adding user
    public function addValidateForm()
    {
        $errors = [];
        $userPost = $this->post['user'];
        //Check if the username already exists
        $checkUsername = $this->usersTable->find('username', $userPost['username']);
        if(empty($userPost['username']))
            $errors[] = 'Username Field is empty';
        else if ($checkUsername != null)
            $errors[] = 'Username already exists';

        if(empty($userPost['password']))
            $errors[] = 'Password Field is empty';

        //Check if the passwords match
        if (!empty($userPost['password']) && !empty($this->post['confirm_password'])) {
            if ($userPost['password'] !== $this->post['confirm_password'])
                $errors[] = 'Mismatched Password';
        }

        //Check if any of the fields are empty
        if($userPost['type']=="")
            $errors[] = 'Account Type not selected';

        if($userPost['status']=="")
            $errors[] = 'Account status not selected';

        return $errors;
    }

    //When a form is submitted. For Adding Jobs
    public function addSave()
    {
        //Validate the form for errors
        $errors = $this->addValidateForm();
        $post = $this->post['user'];

        if (count($errors)== 0){
            //Add up all the permissions set. To be inputted into the database
            $setPermissions = array_sum($this->post['permissions'] ?? []);
            $post['permissions'] = $setPermissions;
            //Encrypt the password
            $post['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
            $this->usersTable->save($post);
            header('location: /admin/users/list');
        } else {
            return $this->add($errors);
        }
    }

    public function delete()
    {
        $post = $this->post['user'];
        $this->usersTable->delete($post['id']);
        header('location: /admin/users/list');
    }

    //Get all the Permission values that are used
    public function getPermissionValues()
    {
        $reflected = new \ReflectionClass('\Jobs\Entity\Users');
        $constants = $reflected->getConstants();
        return $constants;
        //reflects the class. and gets all the constants into an array
    }


}