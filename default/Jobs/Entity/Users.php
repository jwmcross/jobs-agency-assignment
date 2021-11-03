<?php
namespace Jobs\Entity;
use \Core\DatabaseTable;
class Users
{

    const MANAGE_JOBS = 2;
    const MANAGE_APPLICANTS = 4;
    const MANAGE_CATEGORIES = 8;
    const VIEW_ENQUIRIES = 16;
    const MANAGE_ENQUIRIES = 32;
    const VIEW_CLIENTS = 64;
    const MANAGE_USERS = 128;

    public $id;
    public $username;
    public $password;
    public $permissions;
    public $type;
    public $status;

    private $userTable;

    public function __construct(DatabaseTable $userTable)
    {
        $this->userTable = $userTable;
    }

    public function hasPermission($permission)
    {
        return $this->permissions & $permission;
    }

    public function getAccountType()
    {
        if ($this->type == null) return 'Unassigned';
        if ($this->type == 0) return 'Admin';
        if ($this->type == 1) return 'Staff';
        if ($this->type == 2) return 'Client';
    }

    public function getAccountStatus()
    {
        if ($this->status == 0) return 'Disabled';
        if ($this->status == 1) return 'Active';
    }

}