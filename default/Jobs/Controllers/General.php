<?php
namespace Jobs\Controllers;

class General
{

    private $userTable;
    private $categoryTable;
    private $authentication;


    public function __construct(\Core\DatabaseTable $userTable, \Core\DatabaseTable $categoryTable, \Core\Authentication $authentication)
    {
        $this->userTable = $userTable;
        $this->categoryTable = $categoryTable;
        $this->authentication = $authentication;
    }

    public function home()
    {
        header('location: /');
    }

    public function about()
    {
        $title = "Jo's Jobs - About us";
        $categories = $this->categoryTable->findAll();
        return [

            'template' => '../templates/aboutus.html.php',
            'title' => $title,
            'variables' => [
                'categories'=>$categories
            ]
        ];
    }

    public function faq()
    {
        $title = "Jo's Jobs - FAQ - Coming Soon";
        return [
            'template' => '../templates/faq.html.php',
            'title' => $title,
        ];
    }

    public function contactus()
    {
        $title = "Jo's Jobs - Contact Us";
        return [
            'template' => '../templates/contactus.html.php',
            'title' => $title,
            'variables' => []
        ];
    }

    public function adminHome()
    {
        $user = $this->authentication->getUser();
        $title = "Jo\'s Jobs";
        $mainClass = 'sidebar';

        $accountType = $user->getAccountType();

        return [

            'template' => 'admin/index.html.php',
            'title' => $title,
            'mainClass'=> $mainClass,
            'variables' => [
                'user'=>$user,
                'accountType'=>$accountType,
            ]
        ];
    }

}