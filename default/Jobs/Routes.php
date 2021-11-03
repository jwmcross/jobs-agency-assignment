<?php
namespace Jobs;
use Jobs\Entity\Users;

class Routes implements \Core\Routes {


    private $jobTable;
    private $categoryTable;
    private $userTable;
    private $enquiryTable;
    private $applicantsTable;
    private $authentication;


    public function __construct()
    {
        $pdo = null;
        include '../database.php';

        $this->jobTable = new \Core\DatabaseTable($pdo, 'job', 'id', '\Jobs\Entity\Job', [&$this->categoryTable, &$this->applicantsTable, &$this->authentication]);
        $this->categoryTable = new \Core\DatabaseTable($pdo, 'category', 'id', '\Jobs\Entity\Category', [&$this->jobTable, &$this->authentication]);
        $this->applicantsTable = new \Core\DatabaseTable($pdo, 'applicants', 'id', '\Jobs\Entity\Applicants', [&$this->jobTable]);
        $this->userTable = new \Core\DatabaseTable($pdo, 'users', 'id', '\Jobs\Entity\Users', [&$this->jobTable, &$this->authentication]);
        $this->enquiryTable = new \Core\DatabaseTable($pdo, 'enquiries', 'id', '\Jobs\Entity\Enquiry', [&$this->userTable, &$this->authentication]);
        $this->authentication = new \Core\Authentication($this->userTable, 'username', 'password');

    }
    public function getRoutes(): array
    {
        //INSERT ALL CONTROLLERS
        $jobsController = new \Jobs\Controllers\Job($this->jobTable, $this->categoryTable, $this->authentication, $_GET, $_POST);
        $categoryController = new \Jobs\Controllers\Category($this->categoryTable, $_GET, $_POST);
        $enquiryController = new \Jobs\Controllers\Enquiry($this->enquiryTable, $this->authentication, $_GET, $_POST);
        $usersController = new \Jobs\Controllers\Users($this->userTable, $this->authentication, $_GET, $_POST );
        $applicantsController = new \Jobs\Controllers\Applicants($this->jobTable, $this->applicantsTable, $this->authentication, $_GET, $_POST, $_FILES);
        $siteController = new \Jobs\Controllers\General($this->userTable, $this->categoryTable, $this->authentication);

        //LOGIN CONTROLLER
        $loginController = new \Jobs\Controllers\Login($this->authentication, $_GET, $_POST);

        return $routes = [
            '' => [
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'home'
                ]
            ],
            'home' => [
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'home'
                ]
            ],
            'about-us' => [
                'GET' => [
                    'controller' => $siteController,
                    'function' => 'about'
                ]
            ],
            'faq' => [
                'GET' => [
                    'controller' => $siteController,
                    'function' => 'faq'
                ]
            ],
            'contact-us' => [
                'GET' => [
                    'controller' => $enquiryController,
                    'function' => 'enquiryForm'
                ],
                'POST' => [
                    'controller' => $enquiryController,
                    'function' => 'enquirySubmit'
                ]

            ],
            'contact-us/success' => [
                'GET' => [
                    'controller' => $enquiryController,
                    'function' => 'enquirySuccess'
                ]
            ],
            //=============================== PUBLIC APPLY TO JOB
            'apply' => [
                'GET' => [
                    'controller' => $applicantsController,
                    'function' => 'form'
                ],
                'POST' => [
                    'controller' => $applicantsController,
                    'function' => 'formSubmit'
                ]
            ],
            'apply/success' => [
                'GET' => [
                    'controller' => $applicantsController,
                    'function' => 'submitSuccess'
                ]
            ],
            'apply/error' => [
                'GET' => [
                    'controller' => $applicantsController,
                    'function' => 'submitError'
                ]
            ],
            //=============================== PUBLIC LIST JOBS
            'jobs/list' => [
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'list'
                ]
            ],
            'products/list' => [
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'list'
                ]
            ],
            'checkout' => [
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'list'
                ]
            ],
            'jobs/list/category' => [
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'listCategory'
                ]
            ],
            //=============================== LOGIN FUNCTION
            'login' => [
                'GET' => [
                    'controller' => $loginController,
                    'function' => 'loginForm'
                ],
                'POST' => [
                    'controller' => $loginController,
                    'function' => 'loginSubmit'
                ]

            ],
            'login/success' => [
                'GET' => [
                    'controller' => $loginController,
                    'function' => 'success'
                ],
            ],
            'permissionserror' => [
                'GET' => [
                    'controller' => $loginController,
                    'function' => 'permissionsError'
                ],
            ],
            'logout' => [
                'GET' => [
                    'controller' => $loginController,
                    'function' => 'logout'
                ],
            ],

            //=======================================================
            //  ADMIN PANEL SECTION
            //=======================================================
            //===============================
            //  ADMIN - JOBS
            //===============================
            'admin' => [
                'login' => true,
                'GET' => [
                    'controller' => $siteController,
                    'function' => 'adminHome'
                ],
                'permissions'=> Users::MANAGE_JOBS
            ],
            'admin/jobs' => [
                'login' => true,
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'list'
                ],
                'permissions'=> Users::MANAGE_JOBS
            ],
            'admin/jobs/list' => [
                'login' => true,
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'adminList'
                ],
                'permissions'=> Users::MANAGE_JOBS

            ],
            'admin/jobs/add' => [
                'login' => true,
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'add'
                ],
                'POST' => [
                    'controller' => $jobsController,
                    'function' => 'saveAdd'
                ],
                'permissions'=> Users::MANAGE_JOBS

            ],
            'admin/jobs/edit' => [
                'login' => true,
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'edit'
                ],
                'POST' => [
                    'controller' => $jobsController,
                    'function' => 'saveEdit'
                ],
                'permissions'=> Users::MANAGE_JOBS

            ],
            'admin/jobs/archivejob' => [
                'login' => true,
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'archivedJobs'
                ],
                'POST' => [
                    'controller' => $jobsController,
                    'function' => 'archiveJob'
                ],
                'permissions'=> Users::MANAGE_JOBS

            ],
            'admin/jobs/delete' => [
                'login' => true,
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'deleteJob'
                ],
                'POST' => [
                    'controller' => $jobsController,
                    'function' => 'deleteJob'
                ],
                'permissions'=> Users::MANAGE_JOBS
            ],
            'admin/jobs/archived' => [
                'login' => true,
                'GET' => [
                    'controller' => $jobsController,
                    'function' => 'archivedJobs'
                ],
                'POST' => [
                    'controller' => $jobsController,
                    'function' => 'archivedJobs'
                ],
                'permissions'=> Users::MANAGE_JOBS
            ],
            'admin/jobs/applicants' => [
                'login' => true,
                'GET' => [
                    'controller' => $applicantsController,
                    'function' => 'listApplicants'
                ],
                'permissions'=> Users::MANAGE_APPLICANTS
            ],
            //===============================
            //  ADMIN - CATEGORIES
            //===============================
            'admin/category' => [
                'login' => true,
                'GET' => [
                    'controller' => $categoryController,
                    'function' => 'list'
                ],
                'permissions'=> Users::MANAGE_CATEGORIES

            ],
            'admin/category/list' => [
                'login' => true,
                'GET' => [
                    'controller' => $categoryController,
                    'function' => 'list'
                ],
                'permissions'=> Users::MANAGE_CATEGORIES
            ],
            'admin/category/add' => [
                'login' => true,
                'GET' => [
                    'controller' => $categoryController,
                    'function' => 'add'
                ],
                'POST' => [
                    'controller' => $categoryController,
                    'function' => 'saveAdd'
                ],
                'permissions'=> Users::MANAGE_CATEGORIES
            ],
            'admin/category/edit' => [
                'login' => true,
                'GET' => [
                    'controller' => $categoryController,
                    'function' => 'edit'
                ],
                'POST' => [
                    'controller' => $categoryController,
                    'function' => 'saveEdit'
                ],
                'permissions'=> Users::MANAGE_CATEGORIES
            ],
            'admin/category/delete' => [
                'login' => true,
                'GET' => [
                    'controller' => $categoryController,
                    'function' => 'delete'
                ],
                'POST' => [
                    'controller' => $categoryController,
                    'function' => 'delete'
                ],
                'permissions'=> Users::MANAGE_CATEGORIES
            ],
            //===============================
            //  ADMIN - USERS
            //===============================
            'admin/users' => [
                'login' => true,
                'GET' => [
                    'controller' => $usersController,
                    'function' => 'list'
                ],
                'permissions'=> Users::MANAGE_USERS
            ],
            'admin/users/list' => [
                'login' => true,
                'GET' => [
                    'controller' => $usersController,
                    'function' => 'list'
                ],
                'permissions'=> Users::MANAGE_USERS
            ],
            'admin/users/staff' => [
                'login' => true,
                'GET' => [
                    'controller' => $usersController,
                    'function' => 'listStaff'
                ],
                'permissions'=> Users::MANAGE_USERS
            ],
            'admin/users/clients' => [
                'login' => true,
                'GET' => [
                    'controller' => $usersController,
                    'function' => 'listClients'
                ],
                'permissions'=> Users::VIEW_CLIENTS
            ],
            'admin/users/add' => [
                'login' => true,
                'GET' => [
                    'controller' => $usersController,
                    'function' => 'add'
                ],
                'POST' => [
                    'controller' => $usersController,
                    'function' => 'addSave'
                ],
                'permissions'=> Users::MANAGE_USERS

            ],
            'admin/users/edit' => [
                'login' => true,
                'GET' => [
                    'controller' => $usersController,
                    'function' => 'edit'
                ],
                'POST' => [
                    'controller' => $usersController,
                    'function' => 'edit'
                ],
                'permissions'=> Users::MANAGE_USERS
            ],
            'admin/users/edit/save' => [
                'login' => true,
                'GET' => [
                    'controller' => $usersController,
                    'function' => 'edit'
                ],
                'POST' => [
                    'controller' => $usersController,
                    'function' => 'editSave'
                ],
                'permissions'=> Users::MANAGE_USERS
            ],
            'admin/users/delete' => [
                'login' => true,
                'GET' => [
                    'controller' => $usersController,
                    'function' => 'delete'
                ],
                'POST' => [
                    'controller' => $usersController,
                    'function' => 'delete'
                ],
                'permissions'=> Users::MANAGE_USERS
            ],
            //===============================
            //  ADMIN - ENQUIRIES
            //===============================
            'admin/enquiry/list' => [
                'login' => true,
                'GET' => [
                    'controller' => $enquiryController,
                    'function' => 'list'
                ],
                'POST' => [
                    'controller' => $enquiryController,
                    'function' => 'enquiryResponse'
                ],
                'permissions'=> Users::VIEW_ENQUIRIES
            ],
            'admin/enquiry/complete' => [
                'login' => true,
                'GET' => [
                    'controller' => $enquiryController,
                    'function' => 'completedEnquiry'
                ],
                'permissions'=> Users::MANAGE_ENQUIRIES
            ],
            'admin/enquiry/complete/delete' => [
                'login' => true,
                'GET' => [
                    'controller' => $enquiryController,
                    'function' => 'deleteEnquiry'
                ],
                'POST' => [
                    'controller' => $enquiryController,
                    'function' => 'deleteEnquiry'
                ],
                'permissions'=> Users::MANAGE_ENQUIRIES
            ]
        ];

    }

    //Get common layout variables for pages. Specific to this website
    public function getlayoutVariables($title = "Jo's Jobs", $output, $class = null) : array
    {
        return [
            'loggedin' => $this->authentication->isLoggedIn(),
            'categories'=>$this->categoryTable->findAll(),
            'output' => $output,
            'title' => $title,
            'mainClass'=>$class ?? null
        ];

    }

    public function getAuthentication(): \Core\Authentication
    {
        return $this->authentication;
    }

    public function checkPermission($permission): bool
    {
        $user = $this->authentication->getUser();

        if ($user && $user->hasPermission($permission)) {
            return true;
        } else {
            return false;
        }

    }

}

