<?php
namespace Jobs\Controllers;
use \Core\DatabaseTable;
use \Core\Authentication;

class Enquiry {

    private $enquiryTable;
    private $authentication;
    private $get;
    private $post;


    public function __construct(DatabaseTable $enquiryTable,
                                Authentication $authentication, array $get=[], array $post=[])
    {
        $this->enquiryTable = $enquiryTable;
        $this->authentication = $authentication;
        $this->get = $get;
        $this->post = $post;
    }

    //Enquiry form
    public function enquiryForm($errors = null)
    {
        return [
            'template' => 'contactus.html.php',
            'title'=> 'Jo\'s Jobs - Contact us',
            'variables'=>[
                'errors'=>$errors ?? null
            ]
        ];
    }

    //After submitted the Enquiry Form via POST
    public function enquirySubmit()
    {
        //Validate the form for errors
        $errors = $this->validateForm();
        $post = $this->post['enquiry'];
        if(count($errors) == 0) {
            $this->enquiryTable->save($post);
            header('location: /contact-us/success');
        } else {
            return $this->enquiryForm($errors);
        }

    }

    //Form validation for enquiries
    public function validateForm()
    {
        $errors = [];
        $post = $this->post['enquiry'];
        if (empty($post['name']))
            $errors[] = 'Name field empty - Must supply a name';

        if (empty($post['email'])) {
            $errors[] = 'Email field empty - Must supply an email';
        } else {
            //If field not empty, validate the input as a email
            if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL))
                $errors[] = 'Not a valid Email Address';
        }

        if(empty($post['telephone']))
            $errors[] = 'Telephone field empty - Must supply a telephone number';
        else //If the length of the telephone is less than 12. Invalid UK number
            if(strlen($post['telephone']) < 11)
                $errors[] = 'Invalid telephone number. Must be atleast 11 digits long';

        if(empty($post['enquiry_details']))
            $errors[] = 'Enquiry field empty - Must supply an enquiry';

        return $errors;

    }

    public function enquirySuccess()
    {
        $title = 'Jo\'s Jobs - Enquiry Successfully Submitted';

        return [
            'template' => 'enquirysubmitsuccess.html.php',
            'title'=> $title,
        ];
    }

    //List all the Enquiries in the admin panel
    public function list()
    {
        $title = 'Jo\'s Jobs - Admin - Enquiries';
        $mainClass = 'sidebar';
        $user = $this->authentication->getUser();

        //Get all the enquiries that have not been marked as complete
        $enquiries = $this->enquiryTable->find('complete',0);

        return [
            'template' => 'admin/enquirylist.html.php',
            'title'=> $title,
            'mainClass'=> $mainClass,
            'variables' => [
                'enquiries' => $enquiries,
                'user'=>$user ?? null
            ]
        ];
    }

    //Marking the Enquiry As complete
    public function enquiryResponse()
    {
        $post = $this->post['enquiry'];
        $post['complete'] = 1;
        $post['userId'] = $this->authentication->getUser()->id;
        $this->enquiryTable->save($post);
        header('location: /admin/enquiry/list');

    }

    //List all the completed Enquiries
    public function completedEnquiry()
    {
        //Get all the enquiries that have been marked as complete
        $enquiries = $this->enquiryTable->find('complete', 1);
        $user = $this->authentication->getUser();
        $title = 'Jo\'s Jobs - Enquiry Completed';
        $mainClass = 'sidebar';
        return [
            'template' => 'admin/listCompleteEnquiries.html.php',
            'title'=> $title,
            'mainClass' => $mainClass,
            'variables' => [
                'enquiries' => $enquiries,
                'user'=>$user ?? null
            ]
        ];
    }

    //Delete Enquiry
    public function deleteEnquiry()
    {
        $post = $this->post['enquiry'];
        $this->enquiryTable->delete($post['id']);
        header('location: /admin/enquiry/complete');
    }

}