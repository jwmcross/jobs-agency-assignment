<?php

require './Jobs/Controllers/Job.php';
class JobTest extends \PHPUnit\Framework\TestCase{

    private $jobTableMock;
    private $categoryTableMock;

    public function setUp()
    {
        $this->jobTableMock = $this->getMockBuilder('\Core\DatabaseTable')->disableOriginalConstructor()->setMethods(['find', 'insert', 'save', 'findAll'])->getMock();
        $this->categoryTableMock = $this->getMockBuilder('\Core\DatabaseTable')->disableOriginalConstructor()->setMethods(['find', 'insert', 'save','findAll'])->getMock();

    }

    public function testAddJobAllEmptyFields()
    {
        $testPost = [
            'job'=>[
                'title'=>'',
                'description'=>'',
                'location'=>'',
                'salary'=>'',
                'categoryId'=>''
            ],
            'closingDate'=>''
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $jobController = new \Jobs\Controllers\Job($this->jobTableMock,$this->categoryTableMock, $authentication, [], $testPost);

        $result = $jobController->validateForm();

        $this->assertEquals(count($result), 6);
        $this->assertEquals($result[0], 'Missing Field - Title');
        $this->assertEquals($result[1], 'Missing Field - Description');
        $this->assertEquals($result[2], 'Missing Field - Location');
        $this->assertEquals($result[3], 'Missing Field - Salary');
        $this->assertEquals($result[4], 'Must select a category');
        $this->assertEquals($result[5], 'Must select a closing date');

    }

    public function testAddJobInvalidDateType()
    {
        $testPost = [
            'job'=>[
                'title'=>'Test Title',
                'description'=>'Test Description Description Test',
                'location'=>'Somewhere',
                'salary'=>'£10,000 - $99,999',
                'categoryId'=>'1'
            ],
            'closingDate'=>'wrongdate'
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $jobController = new \Jobs\Controllers\Job($this->jobTableMock,$this->categoryTableMock, $authentication, [], $testPost);

        $result = $jobController->validateForm();

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Invalid Date Set');

    }

    public function testAddJobWrongDate()
    {
        $testPost = [
            'job'=>[
                'title'=>'Test Title',
                'description'=>'Test Description Description Test',
                'location'=>'Somewhere',
                'salary'=>'£10,000 - $99,999',
                'categoryId'=>'1'
            ],
            'closingDate'=>'12/12/2000'
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $jobController = new \Jobs\Controllers\Job($this->jobTableMock,$this->categoryTableMock, $authentication, [], $testPost);

        $result = $jobController->validateForm();

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Closing Date needs to be later than the current date');

    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testJobFailedSubmit()
    {
        $testPost = [
            'job'=>[
                'title'=>'Test Title',
                'description'=>'Test Description Description Test',
                'location'=>'',
                'salary'=>'£10,000 - $99,999',
                'categoryId'=>'1',
            ],
            'closingDate'=>'2999/12/12'
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $jobController = new \Jobs\Controllers\Job($this->jobTableMock,$this->categoryTableMock, $authentication, [], $testPost);

        $result = $jobController->save();

        $this->assertEquals($result['template'], 'admin/editjob.html.php');
        $this->assertEquals($result['variables']['errors'][0], 'Missing Field - Location');

    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testJobSuccessfulSubmit()
    {
        $testPost = [
            'job'=>[
                'title'=>'Test Title',
                'description'=>'Test Description Description Test',
                'location'=>'Somewhere',
                'salary'=>'£10,000 - $99,999',
                'categoryId'=>'1',
            ],
            'closingDate'=>'2999/12/12'
        ];

        $expectedData =
            [
                'title'=>'Test Title',
                'description'=>'Test Description Description Test',
                'location'=>'Somewhere',
                'salary'=>'£10,000 - $99,999',
                'categoryId'=>'1',
                'closingDate'=>'2999-12-12',
                'userId'=>'1'
            ];

        $this->jobTableMock->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo($expectedData)
            );

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->setMethods(['getUser'])->getMock();

        $authentication->expects($this->once())->method('getUser')->will($this->returnValue((object) ['id'=>1]));

        $jobController = new \Jobs\Controllers\Job($this->jobTableMock,$this->categoryTableMock, $authentication, [], $testPost);

        $jobController->save();

        $this->assertContains('location: /admin/jobs/list', xdebug_get_headers());
    }


}