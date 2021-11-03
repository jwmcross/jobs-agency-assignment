<?php

namespace Jobs\Controllers;
use \Core\DatabaseTable;
use \Core\Authentication;
use DateTime;
use Exception;

class Job
{
    private $get;
    private $post;

    private $jobTable;
    private $categoryTable;
    private $authentication;

    public function __construct(DatabaseTable $jobTable, DatabaseTable $categoryTable,
                                Authentication $authentication,
                                array $get=[], array $post=[])
    {
        $this->get = $get;
        $this->post = $post;
        $this->jobTable = $jobTable;
        $this->categoryTable = $categoryTable;
        $this->authentication = $authentication;
    }

    //function for the home page of the website
    public function home()
    {
        //Find all the Jobs that have not closed. (Before the closing date of the job)
        //Ordered by the closingDate column, in Ascending order. Limiting to 10 results
        $jobs = $this->jobTable->findAllOptions('closingDate', '>', date('Y-m-d'),
            'closingDate', 'ASC', '10');
        $title = 'Jo\'s Jobs - Welcome';
        return [
            'template'=>'home.html.php',
            'title'=> $title,
            'variables'=> [
                'jobs'=>$jobs,
                'category'=>$category ?? null,
                'locations'=>$locations ?? null
            ]
        ];

    }

    //List the jobs according to the filters set by the user
    public function list() {
        //Find all the Jobs that have not closed. (Before the closing date of the job)
        //Ordered by the closingDate column, in Ascending order. Limiting to 10 results
        $getJobs = $this->jobTable->findAllOptions('closingDate', '>', (new DateTime())->format('Y-m-d'),
            'closingDate', 'ASC');

        //Filter the jobs by location if selected by the user.
        if (isset($this->get['location']) && $this->get['location']!==''){
            $jobs = array_filter($getJobs, function ($filter) {
                return $filter->location == $this->get['location'];
            });
        } else {
            //Return all jobs if location filter isnt selected
            $jobs = $getJobs;
        }

        //Get all the categories to list in sidebar
        $categories = $this->categoryTable->findAll();
        //Get the category name
        $category = (isset($this->get['categoryId'])) ? $this->categoryTable->findById($this->get['categoryId']) : null;
        $title = 'Jo\'s Jobs - Job Listings';
        $mainClass = 'sidebar';
        $locations = $this->getLocations();
        return [
            'template'=>'listjobs.html.php',
            'title'=> $title,
            'mainClass' => $mainClass,
            'variables'=> [
                'jobs'=>$jobs ?? null,
                'categories'=>$categories ?? null,
                'locations' => $locations,
                'category'=>$category ?? null
            ]

        ];


    }

    public function listCategory()
    {
        //If category is not selected. Redirect to jobs list
        if (!isset($this->get['categoryId']))
            header('Location: /jobs/list');

        //Find all the Jobs that have not closed. (Before the closing date of the job)
        //Ordered by the closingDate column, in Ascending order.
        //Get all the jobs by category. And filtered by their closing date in the entity
        $getJobs = $this->jobTable->findAllOptions('closingDate', '>', (new DateTime())->format('Y-m-d'),
            'closingDate', 'ASC');
        //Filter out jobs that dont match the category
        $categoryjobs = array_filter($getJobs, function($job){
            return $job->categoryId == $this->get['categoryId'];
        });

        //Filter the jobs by location if selected by the user.
        if (isset($this->get['location']) && $this->get['location']!==''){
            $jobs = array_filter($categoryjobs, function ($job) {
                return $job->location == $this->get['location'];
            });
        } else {
            //Return all jobs if location filter isnt selected
            $jobs = $categoryjobs;
        }

        //Get all the categories to list in sidebar
        $categories = $this->categoryTable->findAll();
        //Get the category name
        $category = (isset($this->get['categoryId'])) ? $this->categoryTable->findById($this->get['categoryId']) : null;
        $title = 'Jo\'s Jobs - '.$category->name.' Jobs';
        $mainClass = 'sidebar';
        $locations = $this->getLocations();
        return [
            'template'=>'listjobs.html.php',
            'title'=> $title,
            'mainClass' => $mainClass,
            'variables'=> [
                'jobs'=>$jobs ?? null,
                'categories'=>$categories ?? null,
                'locations' => $locations,
                'category'=>$category ?? null
            ]
        ];

    }

    //List the jobs in the admin panel
    public function adminList()
    {
        $user = $this->authentication->getUser();

        $title = 'Jo\'s Jobs - Active Jobs';
        $mainClass = 'sidebar';
        $heading = 'Active Jobs';
        $categories = $this->categoryTable->findAll();

        //If the user is a client. Only retrieve jobs belonging to them.
        if ($user->getAccountType() == 'Client') {
            $getJobs = $this->jobTable->findMultipleConditions(['userId' => $user->id, 'active'=>1]);
        } else {
            //Get all the jobs that are active
            $getJobs = $this->jobTable->find('active', 1);
        }

        //If category is set. And if it is not empty.
        //Filter all the jobs that matches the category ID
        if (isset($this->get['category']) && $this->get['category']!='') {
            $jobs = [];
            foreach ($getJobs as $job):
                if ($job->categoryId == $this->get['category'])
                    $jobs[] = $job;
            endforeach;
            $category = $this->categoryTable->findById($this->get['category']);
            $title = 'Jo\'s Jobs - Active Jobs List for' . $category->name;
        } else {
            $jobs = $getJobs;
        }

        //Get all the unique locations in the database
        $locations = $this->getLocations();

        return [
            'template'=>'admin/listjobs.html.php',
            'title'=> $title,
            'mainClass' => $mainClass,
            'variables'=> [
                'jobs'=>$jobs,
                'categories'=>$categories ?? null,
                'locations'=>$locations ?? null,
                'heading'=>$heading
            ]
        ];
    }


    //List of the all jobs that are archived
    public function archivedJobs()
    {

        $user = $this->authentication->getUser();

        //If the user is a client. Only retrieve jobs belonging to them.
        if ($user->getAccountType() == 'Client') {
            $jobs = $this->jobTable->findMultipleConditions(['userId' => $user->id, 'active'=>0]);
        } else {
            //Get the jobs that are not active
            $jobs = $this->jobTable->find('active', 0);
        }

        $title = 'Jo\'s Jobs - Archived Jobs';
        $mainClass = 'sidebar';
        $heading = 'Archived Jobs';
        $locations = $this->getLocations();
        $categories = $this->categoryTable->findAll();

        return [
            'template'=>'admin/listjobs.html.php',
            'title'=> $title,
            'mainClass' => $mainClass,
            'variables'=> [
                'jobs'=>$jobs,
                'categories'=>$categories ?? null,
                'locations'=>$locations ?? null,
                'heading'=>$heading
            ]
        ];
    }


    //Archive a job
    public function archiveJob()
    {
        $record = $this->post['job'];
        $date = new DateTime();
        //Change the closing date to the current date to remove it from the listings
        $record['closingDate'] = $date->format('Y-m-d');
        //Change the active status to false
        $record['active'] = 0;
        $this->jobTable->save($record);
        header('location: /admin/jobs/list');

    }

    //This function when Form is submitted for editting/adding job
    public function saveAdd()
    {
        //Validate the form
        $errors = $this->validateForm();
        $post = $this->post['job'];

        if (count($errors) == 0){
            //Add User Id to new job only
            $post['userId'] = $this->authentication->getUser()->id;
            //Format the date to SQL format
            $closingDate = new DateTime($this->post['closingDate']);
            $post['closingDate'] = $closingDate->format('Y-m-d');

            $this->jobTable->save($post);
            header('location: /admin/jobs/list');
        } else {
            return $this->add($errors);
        }
    }

    public function saveEdit()
    {
        //Validate the form
        $errors = $this->validateForm();
        $post = $this->post['job'];

        if (count($errors) == 0){
            //Format the date to SQL format
            $closingDate = new DateTime($this->post['closingDate']);
            $post['closingDate'] = $closingDate->format('Y-m-d');

            $this->jobTable->save($post);
            header('location: /admin/jobs/list');
        } else {
            return $this->edit($errors);
        }
    }

    //Validate the form when adding or editting a job
    public function validateForm()
    {
        $jobPost = $this->post['job'];
        $errors = [];

        //Check for any empty fields
        if (empty($jobPost['title'])) $errors[] = 'Missing Field - Title';
        if (empty($jobPost['description'])) $errors[] = 'Missing Field - Description';
        if (empty($jobPost['location'])) $errors[] = 'Missing Field - Location';
        if (empty($jobPost['salary'])) $errors[] = 'Missing Field - Salary';
        if (empty($jobPost['categoryId'])) $errors[] = 'Must select a category';
        if (empty($this->post['closingDate'])) $errors[] = 'Must select a closing date';

        //Check if the date is set and valid
        //Must be checked last
        if (!empty($this->post['closingDate']))
        {
            try {
                $closingDate = new DateTime($this->post['closingDate']);
            } catch (Exception $e) {
                //Catch Error if date is invalid, And return the errors
                //Checking the date must be last for this function
                $errors[] = 'Invalid Date Set';
                return $errors;
            }
            //If the closing date is less than the current date.
            $date = new DateTime();
            if ($closingDate < $date)
                $errors[] = 'Closing Date needs to be later than the current date';
        }

        return $errors;
    }

    //Form for editting a current job
    public function edit($errors = null)
    {

        $title = 'Jo\'s Jobs - Admin - Edit Job';
        $heading = 'Edit Job';
        $mainClass = 'sidebar';
        $action = 'edit';
        $categories = $this->categoryTable->findAll();

        if(isset($this->get['jobId']))
            $job = $this->jobTable->findById($this->get['jobId']);
        else if(isset($this->post['job']['id']))
            $job = $this->jobTable->findById($this->post['job']['id']);
        //If job Id is not set. load the add form
        else header('location: /admin/jobs/add');

        $user = $this->authentication->getUser();

        //Check if the user is a client
        //Check if the user is the owner of the job to edit.
        if ($user->getAccountType() == 'Client') {
            if ($job->userId !== $user->id) header('location: /permissionserror');
        }

        return [
            'template' => 'admin/editjob.html.php',
            'title'=> $title,
            'mainClass' => $mainClass,
            'variables' => [
                'job' => $job ?? null,
                'categories'=>$categories,
                'heading'=>$heading,
                'errors'=>$errors ?? null,
                'action'=>$action
            ]
        ];
    }

    //Form for adding a new jobs
    public function add($errors = null)
    {
        $title = 'Jo\'s Jobs - Admin - Add Job';
        $heading = 'Add Job';
        $mainClass = 'sidebar';
        $action = 'add';
        $categories = $this->categoryTable->findAll();
        $user = $this->authentication->getUser();

        return [
            'template' => 'admin/editjob.html.php',
            'title'=> $title,
            'mainClass' => $mainClass,
            'variables' => [
                'job' => $job ?? null,
                'heading'=>$heading ?? null,
                'categories'=>$categories,
                'errors'=>$errors ?? null,
                'user'=>$user,
                'action'=>$action
            ]
        ];

    }

    public function deleteJob()
    {
        $post = $this->post['job'];
        $this->jobTable->delete($post['id']);
        header('location: /admin/jobs/list');
    }

    //Get all the unique locations
    private function getLocations()
    {
        return  $this->jobTable->distinct('location');
    }

}