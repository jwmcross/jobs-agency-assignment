<?php


namespace Jobs\Entity;
use Core\DatabaseTable;
use DateTime;

class Job
{

    public $id;
    public $title;
    public $description;
    public $salary;
    public $closingDate;
    public $categoryId;
    public $location;

    private $categoryTable;
    private $applicantsTable;
    private $authentication;
    public $category;
    public $applicants;

    public function __construct(DatabaseTable $categoryTable, DatabaseTable $applicantsTable,
                                \Core\Authentication $authentication)
    {
        $this->categoryTable = $categoryTable;
        $this->authentication = $authentication;
        $this->applicantsTable = $applicantsTable;

    }

    //Get the category information for this job.
    public function getCategory()
    {
        return $this->categoryTable->find('id',$this->categoryId)[0];
    }

    //Get the client for this job
    public function getClient()
    {
        return $this->userTable->findById($this->userId);
    }

    //Get applicants for this job
    public function getApplicants()
    {
        return $this->applicantsTable->find('jobId', $this->id);
    }

    //Count the number of Applicants for this job
    public function countApplicants()
    {
        $this->applicants = $this->applicantsTable->count('jobId', $this->id)[0];
        return $this->applicants ?? '0';
    }

}