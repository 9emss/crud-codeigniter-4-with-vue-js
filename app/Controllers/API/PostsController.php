<?php

namespace App\Controllers\API;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class PostsController extends ResourceController
{
    use ResponseTrait;
    protected $modelName = 'App\Models\PostsModel';
    protected $format = 'json';

    /**
     * index function
     * 
     * @method : GET
     */
    public function index()
    {
        return $this->respond([
            'statusCode' => 200,
            'message' => 'OK',
            'data' => $this->model->orderBy('id', 'DESC')->findAll()
        ], 200);
    }

    /**
     * show function
     * 
     * @method : GET with params ID
     */
    public function show($id = null)
    {

        $data = $this->model->find($id);

        if (!$data)
            return $this->failNotFound('Post nof found!');

        return $this->respond([
            'statusCode' => 200,
            'message' => 'OK',
            'data' => $data
        ], 200);
    }

    /**
     * create function
     * 
     * @method : POST
     */
    public function create()
    {

        // validation
        $validation = $this->validate([
            'title' => 'required|alpha_numeric_space',
            'description' => 'required'
        ]);

        if (!$validation)
            return $this->failValidationErrors($this->validator->getErrors());

        if ($this->request) {
            // get request from vue js
            if ($this->request->getJSON()) {
                $json = $this->request->getJSON();

                $post = $this->model->insert([
                    'title' => $json->title,
                    'description' => $json->description
                ]);

            } else {
                // get request from postman and more
                $post = $this->model->insert([
                    'title' => $this->request->getPost('title'),
                    'description' => $this->request->getPost('description'),
                ]);
            }

            if (!$post)
                return $this->respond([
                    'statusCode' => 400,
                    'message' => 'Failed to save post!'
                ], 400);

            return $this->respond([
                'statusCode' => 201,
                'message' => 'Data have been saved!'
            ], 201);
        }

        return $this->fail('Error Post not created!');
    }

    /**
     * update function
     * 
     * @method : PUT or PATCH
     */
    public function update($id = null)
    {
        $post = $this->model;

        if ($this->request) {

            //get request from Vue Js
            if ($this->request->getJSON()) {
                $json = $this->request->getJSON();

                // dd();
                // $id = $this->request->getVar('id');

                $data = [
                    'title' => $json->title,
                    'description' => $json->description
                ];

                $post->update($id, $data);


            } else {
                $data = $this->request->getRawInput();
                $post->update($id, $data);
            }

            return $this->respond([
                'statusCode' => 201,
                'message' => 'Data have been updated!'
            ], 201);
        }
    }

    /**
     * delete function
     * 
     * @method : DELETE
     */
    public function delete($id = null)
    {
        $post = $this->model->find($id);

        if (!$post)
            return $this->respond([
                'statusCode' => 400,
                'message' => 'Post not found!'
            ], 400);

        $this->model->delete($id);
        return $this->respond([
            'statusCode' => 201,
            'message' => 'Data have been deleted!'
        ], 201);
    }
}