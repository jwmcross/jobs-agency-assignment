<?php

require './Jobs/Controllers/Applicants.php';
class ApplyTest extends \PHPUnit\Framework\TestCase{

    private $applicantTableMock;
    private $jobTableMock;

    public function setUp()
    {
        $this->applicantTableMock = $this->getMockBuilder('\Core\DatabaseTable')->disableOriginalConstructor()->setMethods(['find', 'insert', 'save'])->getMock();
        $this->jobTableMock = $this->getMockBuilder('\Core\DatabaseTable')->disableOriginalConstructor()->setMethods(['find', 'insert', 'save'])->getMock();
    }

    public function testApplyValidateFormAllEmptyFields()
    {
        $testPost = [
            'applicant'=>[
                'name'=>'',
                'email'=>'',
                'details'=>'',
                'jobId'=>''
            ]
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $applicantController = new \Jobs\Controllers\Applicants($this->jobTableMock,$this->applicantTableMock, $authentication, [], $testPost);

        $result = $applicantController->validateForm();

        $this->assertEquals(count($result), 4);
        $this->assertEquals($result[0], 'Name field empty - Must supply a name');
        $this->assertEquals($result[1], 'Email field empty - Must supply a a valid email');
        $this->assertEquals($result[2], 'Details field empty');
        $this->assertEquals($result[3], 'Error Loading Job Details');

    }

    public function testApplyValidateFormInvalidEmail()
    {
        $testPost = [
            'applicant'=>[
                'name'=>'Test Person',
                'email'=>'notvalidemail',
                'details'=>'Test Details Test information',
                'jobId'=>'1'
            ]
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $applicantController = new \Jobs\Controllers\Applicants($this->jobTableMock,$this->applicantTableMock, $authentication, [], $testPost);

        $result = $applicantController->validateForm();

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Not a valid Email Address');
    }

    public function testApplyValidateFormMissingJobId()
    {
        $testPost = [
            'applicant'=>[
                'name'=>'Test Person',
                'email'=>'test@test.com',
                'details'=>'Test Details Test information',
                'jobId'=>''
            ]
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $applicantController = new \Jobs\Controllers\Applicants($this->jobTableMock,$this->applicantTableMock, $authentication, [], $testPost);

        $result = $applicantController->validateForm();

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Error Loading Job Details');

    }

    public function testApplyValidateFormAllFieldsValid()
    {
        $testPost = [
            'applicant'=>[
                'name'=>'Test Person',
                'email'=>'test@test.com',
                'details'=>'Test Details Test information',
                'jobId'=>'1'
            ]
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $applicantController = new \Jobs\Controllers\Applicants($this->jobTableMock,$this->applicantTableMock, $authentication, [], $testPost);

        $result = $applicantController->validateForm();

        $this->assertEquals(count($result), 0);
    }


    /**
     * @test
     * @runInSeparateProcess
     */
    public function testApplyFailSubmit()
    {
        $testPost = [
            'applicant'=>[
                'name'=>'Test Person',
                'email'=>'test@test.com',
                'details'=>'Test Details Test information',
                'jobId'=>'1'
            ]
        ];

        $testFiles = [
            'cv'=> [
                'error'=>1,
                'name'=>'testdoc.doc',
                'tmp_name'=>'tmptestdoc.doc',
            ]
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $applicantController = new \Jobs\Controllers\Applicants($this->jobTableMock,$this->applicantTableMock, $authentication, [], $testPost, $testFiles);
        $result = $applicantController->formSubmit();

        $this->assertContains('location: /apply/error', xdebug_get_headers());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testApplySuccessfulSubmit()
    {
        $testPost = [
            'applicant'=>[
                'name'=>'Test Person',
                'email'=>'test@test.com',
                'details'=>'Test Details Test information',
                'jobId'=>'1'
            ]
        ];

        $testFiles = [
            'cv'=> [
                'error'=>0,
                'name'=>'testdoc.doc',
                'tmp_name'=>'tmptestdoc.doc',
            ]
        ];

        $this->applicantTableMock->expects($this->once())->method('save');

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $applicantController = new \Jobs\Controllers\Applicants($this->jobTableMock,$this->applicantTableMock, $authentication, [], $testPost, $testFiles);
        $result = $applicantController->formSubmit();

        $this->assertContains('location: /apply/success', xdebug_get_headers());
    }


}