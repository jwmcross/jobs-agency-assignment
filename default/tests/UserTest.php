<?php

require './Jobs/Controllers/Users.php';
require './Jobs/Entity/Users.php';
class UserTest extends \PHPUnit\Framework\TestCase{

    private $userTableMock;

    public function setUp()
    {
        $this->userTableMock = $this->getMockBuilder('\Core\DatabaseTable')->disableOriginalConstructor()->setMethods(['find','findById', 'insert', 'save'])->getMock();
    }

    public function testAddUserValidateFormAllFieldsEmpty()
    {
        $testPost = [
            'user'=>[
                'username'=>'',
                'password'=>'',
                'type'=>'',
                'status'=>''
            ],
            'confirm_password'=>''
        ];
        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);

        $result = $userController->addValidateForm($testPost);

        $this->assertEquals(count($result), 4);
        $this->assertEquals($result[0], 'Username Field is empty');
        $this->assertEquals($result[1], 'Password Field is empty');
        $this->assertEquals($result[2], 'Account Type not selected');
        $this->assertEquals($result[3], 'Account status not selected');

    }

    public function testAddUserValidateFormMismatchPasswords()
    {
        $testPost = [
            'user'=>[
                'username'=>'username',
                'password'=>'password',
                'type'=>'1',
                'status'=>'1'
            ],
            'confirm_password'=>'wrongpassword'
        ];
        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);

        $result = $userController->addValidateForm();

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Mismatched Password');
        
    }

    public function testAddUserValidateFormUserExists()
    {
        $testPost = [
            'user'=>[
                'username'=>'admin',
                'password'=>'password',
                'type'=>'1',
                'status'=>'1'
            ],
            'confirm_password'=>'password'
        ];

        $rtnData = 
            (object) [
                'username'=>'admin',
            
        ];

        $this->userTableMock->expects($this->once())
            ->method('find')            
            ->with(
                $this->equalTo('username'),
                $this->equalTo($testPost['user']['username'])
            )
            ->willReturn($rtnData);


        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);

        $result = $userController->addValidateForm($testPost);

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Username already exists');
        
    }

    public function testAddUserValidateFormEmptyTypeAndStatus()
    {
        $testPost = [
            'user'=>[
                'username'=>'username',
                'password'=>'password',
                'type'=>'',
                'status'=>''
            ],
            'confirm_password'=>'password'
        ];
        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);
        //$mock = $this->getMockBuilder('\Jobs\Controllers\Users')->disableOriginalConstructor()->getMock();

        $result = $userController->addValidateForm($testPost);

        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0], 'Account Type not selected');
        $this->assertEquals($result[1], 'Account status not selected');
        
    }

    public function testAddUserValidateFormAllFieldsEnteredNoErrors()
    {
        $testPost = [
            'user'=>[
                'username'=>'newuser',
                'password'=>'password',
                'type'=>'1',
                'status'=>'1'
            ],
            'confirm_password'=>'password'
        ];
        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);
        //$mock = $this->getMockBuilder('\Jobs\Controllers\Users')->disableOriginalConstructor()->getMock();

        $result = $userController->addValidateForm($testPost);

        $this->assertEquals(count($result), 0);
        
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testAddUserSaveUserNoErrors()
    {
        $testPost = [
            'user'=>[
                'username'=>'user',
                'password'=>'secret',
                'type'=>'1',
                'status'=>'1',
                'permissions'=>2
            ],
            'confirm_password'=>'secret',
            'permissions'=>[2],
            ];

        $this->userTableMock->expects($this->once())->method('save');

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);

        $result = $userController->addSave();
        $this->assertContains('location: /admin/users/list', xdebug_get_headers());

    }

    
    /**
     * @test
     * @runInSeparateProcess
     */
    public function testAddUserSaveUserWithErrors()
    {
        $testPost = [
            'user'=>[
                'username'=>'user',
                'password'=>'secret',
                'type'=>'1',
                'status'=>'1',
                'permissions'=>2
            ],
            'confirm_password'=>'wrongpasss',
            'permissions'=>[2],
            ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);

        $result = $userController->addSave();
        $this->assertEquals($result['template'], 'admin/edituser.html.php');
        
    }


    ///////////////
    //Edit User
    ///////////////
    public function testEditUserValidateFormUserExists()
    {
        $testPost = [
            'user'=>[
                'id'=>'1',
                'username'=>'username',
                'password'=>'password',
                'type'=>'',
                'status'=>''
            ],
            'confirm_password'=>'password',
            'old_username'=>'admin'
        ];

        $rtnData = [
            (object) [
                'username'=>'admin',
            ]
            ];

        $this->userTableMock->expects($this->once())
            ->method('find')            
            ->with(
                $this->equalTo('username'),
                $this->equalTo($testPost['user']['username'])
            )
            ->willReturn($rtnData);

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);
        //$mock = $this->getMockBuilder('\Jobs\Controllers\Users')->disableOriginalConstructor()->getMock();

        $result = $userController->editValidateForm();

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Username already exists');
        
    }

    public function testEditUserValidateFormMismatchPassword()
    {
        $testPost = [
            'user'=>[
                'id'=>'1',
                'username'=>'username',
                'password'=>'password',
                'type'=>'',
                'status'=>''
            ],
            'confirm_password'=>'wrongpassword',
            'old_username'=>'username'
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);
        //$mock = $this->getMockBuilder('\Jobs\Controllers\Users')->disableOriginalConstructor()->getMock();

        $result = $userController->editValidateForm();

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Mismatched Password');
        
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testEditUserSaveUserWithErrors()
    {
        $testPost = [
            'user'=>[
                'id'=>'1',
                'username'=>'username',
                'password'=>'secret',
                'type'=>'1',
                'status'=>'1',
                'permissions'=>2
            ],
            'confirm_password'=>'wrongpasss',
            'old_username'=>'username',
            'permissions'=>[2],
            ];

        $getData = ['id'=>'1'];
        
        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, $getData, $testPost);

        $result = $userController->editSave();
        $this->assertEquals($result['template'], 'admin/edituser.html.php');
        
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testEditUserSaveUserNoErrors()
    {
        $testPost = [
            'user'=>[
                'id'=>'1',
                'username'=>'username',
                'password'=>'newpassword',
                'type'=>'1',
                'status'=>'1',
                'permissions'=>2
            ],
            'confirm_password'=>'newpassword',
            'old_username'=>'username',
            'permissions'=>[2],
            ];

        $this->userTableMock->expects($this->once())->method('save');

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $userController = new \Jobs\Controllers\Users($this->userTableMock,$authentication, [], $testPost);

        $result = $userController->editSave();
        $this->assertContains('location: /admin/users/list', xdebug_get_headers());
        
    }



}