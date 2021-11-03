<?php

namespace Jobs\Controllers;
use \Core\DatabaseTable;
use \Core\Authentication;

class Applicants
{
    private $get;
    private $post;
    private $files;

    private $jobTable;
    private $applicantTable;
    private $authentication;


    public function __construct(DatabaseTable $jobTable, DatabaseTable $applicantTable,
                                Authentication $authentication, array $get = [], array $post= [], array $files=[])
    {
        $this->get = $get;
        $this->post = $post;
        $this->jobTable = $jobTable;
        $this->applicantTable = $applicantTable;
        $this->authentication = $authentication;
        $this->files = $files;
    }

    //Form For Applicants
    public function form($errors = null)
    {
        //Find the job details for the applicant to apply to
        if (isset($this->get['id'])) {
            $date = new \DateTime();
            $job = $this->jobTable->findById($this->get['id']);
            if($job==null) header('location: /'); //If the job doesnt exist. Goto home page.
            if ($job->closingDate < $date->format('Y-m-d')){
                return $this->jobClosed();
            }
        }

        return [
            'template' => 'apply.html.php',
            'title' => 'Jo\' Jobs - Apply to this job',
            'variables' => [
                'job' => $job ?? '',
                'errors'=>$errors ?? null
            ]
        ];

    }

    public function jobClosed()
    {
        $title = "Jo's Jobs - Job Closed";
        return [
            'template' => 'jobclosed.html.php',
            'title'=> $title
        ];

    }

    //Validatation of the applicants form
    public function validateForm()
    {
        $applicantPost = $this->post['applicant'];
        $errors = [];
        //Build errors if fields are empty or dont meet requirements
        if(empty($applicantPost['name']))
            $errors[] = 'Name field empty - Must supply a name';

        if(empty($applicantPost['email']))
            $errors[] = 'Email field empty - Must supply a a valid email';
        else //If email is not a valid email
            if(!filter_var($applicantPost['email'], FILTER_VALIDATE_EMAIL))
                $errors[] = 'Not a valid Email Address';

        if(empty($applicantPost['details']))
            $errors[] = 'Details field empty';

        if(empty($applicantPost['jobId']))
            $errors[] = 'Error Loading Job Details';

        return $errors;

    }

    //This function after the form has been submiutted via POST
    public function formSubmit()
    {
        $post = $this->post['applicant'];

        //Validate the form for errors
        $errors = $this-> validateForm();

        //If there are no errors with the form. Proceed
        if(count($errors) == 0) {

            //If there are no errors with uploading the document.
            if ($this->files['cv']['error'] == 0) {
                $parts = explode('.', $this->files['cv']['name']);
                $extension = end($parts);

                //give the uploaded document a unique name.
                $filename = $parts[0] . '-' . uniqid() . '.' . $extension;

                move_uploaded_file($this->files['cv']['tmp_name'], 'cvs/' . $filename);

                $post['cv'] = $filename;
                //Insert into database for a new entry of applicant
                $this->applicantTable->save($post);
                header('location: /apply/success');
            } else {
                //If there are errors with the file upload
                header('location: /apply/error');
            }
        } else {
            return $this->form($errors);
        }
    }

    public function submitSuccess()
    {
        return [
            'template' => 'applysuccess.html.php',
            'title' => "Jo's Jobs - Application Submitted"
        ];
    }

    public function submitError()
    {
        return [
            'template' => 'applyError.html.php',
            'title' => "Jo's Jobs - Application Error"
        ];
    }

    //List applicants of a a job
    public function listApplicants()
    {
        //If the jobID is not set in GET. Go back to the list of jobs
        if (!isset($this->get['jobId']) || $this->get['jobId']=='') header('location: /admin/jobs/list');

        //get the job details
        $job = $this->jobTable->findById($this->get['jobId']);
        //get current user
        $user = $this->authentication->getUser();
        //Check if the account is a client account
        if ($user->getAccountType() == 'Client') {
            //Checks if the userID in jobs, matches the current user. Permission error if doesnt match
            if ($job->userId !== $user->id)
                header('location: /permissionserror');
        }
        //get the applicants of the job
        $applicants = $job->getApplicants();


        $title = "Jo's Jobs - Applicants for Job";
        $mainClass = 'sidebar';
        $heading = 'Applicants for job';

        return [
            'template'=>'admin/listapplicants.html.php',
            'title'=> $title,
            'mainClass' => $mainClass,
            'variables'=> [
                'job'=>$job,
                'applicants'=>$applicants ?? null,
                'category'=>$category ?? null,
                'locations'=>$locations ?? null,
                'heading'=>$heading
            ]
        ];
    }
}