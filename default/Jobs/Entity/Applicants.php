<?php
namespace Jobs\Entity;
use Core\DatabaseTable;

class Applicants
{
    private $jobTable;

    public $id;
    public $name;
    public $email;
    public $details;
    public $jobId;
    public $cv;

    public function __construct(DatabaseTable $jobTable)
    {
        $this->jobTable;
    }

    //Get the jobs that applicants have applied for
    public function getJob(){
        $job = $this->jobTable->find('id', $this->jobId)[0];
        return $job;
    }


}