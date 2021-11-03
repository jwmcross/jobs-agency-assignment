<?php

require './Jobs/Controllers/Category.php';
class CategoryTest extends \PHPUnit\Framework\TestCase{


    private $categoryTableMock;

    public function setUp()
    {

        $this->categoryTableMock = $this->getMockBuilder('\Core\DatabaseTable')->disableOriginalConstructor()->setMethods(['find','findById', 'insert', 'save'])->getMock();

    }


    public function testAddEmptyFields()
    {

        $testPost = [
            'category'=>[
                'name'=>''
            ]
        ];

        $categoryController = new \Jobs\Controllers\Category($this->categoryTableMock, [], $testPost);

        $result = $categoryController->validateForm();

        $this->assertEquals(count($result),1);
        $this->assertEquals($result[0], 'Category field cannot be empty');

    }

    public function testAddCategoryAlreadyExists()
    {
        $testPost = [
            'category'=>[
                'name'=>'IT'
            ]
        ];
        $returnData = [
            (object) [
                'name'=> 'IT'
            ]
        ];

        $this->categoryTableMock->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo('name'),
                $this->equalTo($testPost['category']['name'])
            )
            ->willReturn($returnData);

        $categoryController = new \Jobs\Controllers\Category($this->categoryTableMock, [], $testPost);

        $result = $categoryController->validateForm();

        $this->assertEquals(count($result),1);
        $this->assertEquals($result[0], 'Category already exists');

    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testAddSuccessAddCategoryWithErrors()
    {
        $testPost = [
            'category'=>[
                'name'=>''
            ]
        ];

        $categoryController = new \Jobs\Controllers\Category($this->categoryTableMock, ['id'=>1], $testPost);

        $result = $categoryController->saveAdd();

        $this->assertEquals($result['template'], 'admin/editcategory.html.php');

    }


    /**
     * @test
     * @runInSeparateProcess
     */
    public function testAddSuccessAddCategoryNoErrors()
    {
        $testPost = [
            'category'=>[
                'name'=>'Dummy Category'
            ]
        ];
        $this->categoryTableMock->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo($testPost['category'])
            );
        $categoryController = new \Jobs\Controllers\Category($this->categoryTableMock, [], $testPost);

        $result = $categoryController->saveAdd();

        $this->assertContains('location: /admin/category/list', xdebug_get_headers());

    }


    ////////////////////////
    //EDIT TESTING
    ////////////////////////
    public function testEditCategoryEmptyFields()
    {

        $testPost = [
            'category'=>[
                'id'=>'1',
                'name'=>''
            ],
            'old_category'=>''
        ];


        $categoryController = new \Jobs\Controllers\Category($this->categoryTableMock, [], $testPost);

        $result = $categoryController->validateForm();

        $this->assertEquals(count($result),1);
        $this->assertEquals($result[0], 'Category field cannot be empty');
    }

    public function testEditCategoryAlreadyExists()
    {
        $testPost = [
            'category'=>[
                'name'=>'IT'
            ]
        ];
        $returnData = [
            (object) [
                'name'=> 'IT'
            ]
        ];

        $this->categoryTableMock->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo('name'),
                $this->equalTo($testPost['category']['name'])
            )
            ->willReturn($returnData);

        $categoryController = new \Jobs\Controllers\Category($this->categoryTableMock,[], $testPost);

        $result = $categoryController->validateForm();

        $this->assertEquals(count($result),1);
        $this->assertEquals($result[0], 'Category already exists');

    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testEditSuccessAddCategoryWithErrors()
    {
        $testPost = [
            'category'=>[
                'id'=>'1',
                'name'=>''
            ],
            'old_category'=>'Dummy Category'
        ];

        $categoryController = new \Jobs\Controllers\Category($this->categoryTableMock, ['id'=>1], $testPost);

        $result = $categoryController->saveEdit();

        $this->assertEquals($result['template'], 'admin/editcategory.html.php');
    }


    /**
     * @test
     * @runInSeparateProcess
     */
    public function testEditSuccessAddCategoryNoErrors()
    {
        $testPost = [
            'category'=>[
                'id'=>'1',
                'name'=>'Dummy Category'
            ],
            'old_category'=>'Dummy Category'
        ];
        $this->categoryTableMock->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo($testPost['category'])
            );
        $categoryController = new \Jobs\Controllers\Category($this->categoryTableMock, [], $testPost);

        $result = $categoryController->saveEdit();

        $this->assertContains('location: /admin/category/list', xdebug_get_headers());

    }

}