<?php
namespace Jobs\Entity;
use \Core\DatabaseTable;

class Enquiry
{
    public $id;
    public $subject;
    public $name;
    public $email;
    public $telephone;
    public $enquiry_details;
    public $complete;
    public $userId;

    private $userTable;

    public function __construct(DatabaseTable $userTable)
    {
        $this->userTable = $userTable;
    }

    //Get the user that has marked the enquiry as complete
    public function getUser()
    {
        return $this->userTable->findById($this->userId);

    }

}