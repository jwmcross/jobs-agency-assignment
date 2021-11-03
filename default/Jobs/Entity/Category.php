<?php

namespace Jobs\Entity;
use Core\DatabaseTable;
use DateTime;


class Category
{
    public $id;
    public $name;
    private $jobTable;
    public function __construct(DatabaseTable $jobTable)
    {
        $this->jobTable = $jobTable;
    }

    //Get the jobs with this category
    public function getJobs()
    {
        $jobs = $this->jobTable->findAllOptions('categoryId', '=', $this->id, 'closingDate', 'ASC');
        return $jobs;
    }


}