<?php
require './Jobs/Controllers/Enquiry.php';

class EnquiryTest extends \PHPUnit\Framework\TestCase{

    private $enquiryTableMock;

    public function setUp()
    {

        $this->enquiryTableMock = $this->getMockBuilder('\Core\DatabaseTable')->disableOriginalConstructor()->setMethods(['find', 'insert', 'save'])->getMock();

    }

    public function testEnquiryEmptyFields()
    {
        $testPost = [
            'enquiry'=>[
                'name'=>'',
                'email'=>'',
                'telephone'=>'',
                'enquiry_details'=>''
            ]
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $enquiryController = new \Jobs\Controllers\Enquiry($this->enquiryTableMock,$authentication, [], $testPost);

        $result = $enquiryController->validateForm();

        $this->assertEquals(count($result), 4);
        $this->assertEquals($result[0], 'Name field empty - Must supply a name');
        $this->assertEquals($result[1], 'Email field empty - Must supply an email');
        $this->assertEquals($result[2], 'Telephone field empty - Must supply a telephone number');
        $this->assertEquals($result[3], 'Enquiry field empty - Must supply an enquiry');
    }

    public function testEnquiryInvalidEmail()
    {
        $testPost = [
            'enquiry'=>[
                'name'=>'Test',
                'email'=>'notvalidemail',
                'telephone'=>'01234567891011',
                'enquiry_details'=>'Test Details Test Information Test Details Test Information'
            ]
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $enquiryController = new \Jobs\Controllers\Enquiry($this->enquiryTableMock,$authentication, [], $testPost);

        $result = $enquiryController->validateForm();

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Not a valid Email Address');
    }

    public function testEnquiryInvalidTelephoneNumberLength()
    {
        $testPost = [
            'enquiry'=>[
                'name'=>'Test',
                'email'=>'test@test.com',
                'telephone'=>'123456789',
                'enquiry_details'=>'Test Details Test Information Test Details Test Information'
            ]
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $enquiryController = new \Jobs\Controllers\Enquiry($this->enquiryTableMock,$authentication, [], $testPost);

        $result = $enquiryController->validateForm();

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], 'Invalid telephone number. Must be atleast 11 digits long');
    }

    public function testEnquirySubmitWithErrors()
    {
        $testPost = [
            'enquiry'=>[
                'name'=>'Test',
                'email'=>'notvalidemail',
                'telephone'=>'01234567891011',
                'enquiry_details'=>'Test Details Test Information Test Details Test Information'
            ]
        ];

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $enquiryController = new \Jobs\Controllers\Enquiry($this->enquiryTableMock,$authentication, [], $testPost);

        $result = $enquiryController->enquirySubmit();

        $this->assertEquals($result['template'],'contactus.html.php');
        $this->assertEquals($result['variables']['errors'][0],'Not a valid Email Address');
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testEnquirySuccessfullSubmitNoErrors()
    {
        $testPost = [
            'enquiry'=>[
                'name'=>'Test',
                'email'=>'test@test.com',
                'telephone'=>'01234567891011',
                'enquiry_details'=>'Test Details Test Information Test Details Test Information'
            ]
        ];

        $this->enquiryTableMock->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo($testPost['enquiry'])
            );

        $authentication = $this->getMockBuilder('\Core\Authentication')->disableOriginalConstructor()->getMock();
        $enquiryController = new \Jobs\Controllers\Enquiry($this->enquiryTableMock,$authentication, [], $testPost);

        $enquiryController->enquirySubmit();

        $this->assertContains('location: /contact-us/success', xdebug_get_headers());
    }


}



    

