<?php

namespace Jobs\Controllers;
use \Core\DatabaseTable;

class Category
{
    private $categoryTable;
    private $get;
    private $post;

    public function __construct(DatabaseTable $categoryTable, array $get=[], array $post=[])
    {
        $this->categoryTable = $categoryTable;
        $this->get = $get;
        $this->post = $post;

    }

    //List of categories
    public function list()
    {
        //get all the categories in the database
        $categories = $this->categoryTable->findAll();
        $mainClass = 'sidebar';

        return [
            'template' => 'admin/listcategories.html.php',
            'title' => 'Jo\'s Jobs - Admin - Categories',
            'mainClass'=> $mainClass,
            'variables' => [
                'categories' => $categories,
            ]
        ];

    }

    //Category form for editting
    public function edit($errors = null)
    {
        //Get the category information from the database
        if(isset($this->get['id']))
            $category = $this->categoryTable->findById($this->get['id']);
        else if(isset($this->post['category']['id']))
            $category = $this->categoryTable->findById($this->post['category']['id']);
        else header('location: /admin/category/list');
        $title = 'Jo\'s Jobs - Admin - Categories';
        $heading = 'Edit Category';
        $mainClass = 'sidebar';

        //If no category found goto add category page
        if($category == null) header('location: /admin/category/add');

        return [
            'template' => 'admin/editcategory.html.php',
            'title' => $title,
            'mainClass'=> $mainClass,
            'variables' => [
                'category' => $category ?? null,
                'heading'=>$heading,
                'errors'=>$errors ?? null,
                'action'=>'edit'
            ]
        ];
    }

    //Once form is submitted when Editting a category.
    public function saveEdit()
    {
        //Validate the form for errors
        $errors = $this->validateForm();
        $post = $this->post['category'];
        if(count($errors)==0){
            $this->categoryTable->save($post);
            header('location: /admin/category/list');
        } else {
            return $this->edit($errors);
        }
    }

    //Form validation for the categories form
    public function validateForm()
    {
        $errors = [];
        $postCategory = $this->post['category'];
        $category = $this->categoryTable->find('name', $postCategory['name']);
        //When adding a category
        if(!isset($postCategory['id'])) {
            if($category!=null)
                $errors[] = 'Category already exists';
        }
        //When editting a category
        if(isset($postCategory['id'])) {
            //If the category name doesnt match the old name in the form.
            if($postCategory['name'] != $this->post['old_category']) {
                //Check if the new category name already exists
                if ($category != null)
                    $errors[] = 'Category already exists';
            }
        }

        if (empty($postCategory['name']))
            $errors[] = 'Category field cannot be empty';

        return $errors;

    }

    //Form for adding a category
    public function add($errors = null)
    {
        $title = "Jo's Jobs - Admin - Categories";
        $heading = 'Add Category';
        $mainClass = 'sidebar';

        return [
            'template' => 'admin/editcategory.html.php',
            'title' => $title,
            'mainClass'=> $mainClass,
            'variables' => [
                'category' => $category ?? null,
                'heading'=>$heading,
                'errors'=>$errors ?? null,
                'action'=>'add'
            ]
        ];
    }
    //Once form is submitted when Adding a category.
    public function saveAdd()
    {
        //Validate the form for errors
        $errors = $this->validateForm();
        $post = $this->post['category'];
        if(count($errors)==0){
            $this->categoryTable->save($post);
            header('location: /admin/category/list');
        } else {
            return $this->add($errors);
        }

    }

    public function delete()
    {
        $this->categoryTable->delete($this->post['id']);
        header('location: /admin/category');
    }

}