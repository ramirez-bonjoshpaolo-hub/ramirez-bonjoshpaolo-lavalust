<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('LabApi');
        $this->call->database();
        $this->call->model('ProductModel');
        $this->call->library('ProductInput');
    }
    private function product($id): array
    {
        $product = $this->ProductModel->find((int) $id);
        if (!$product) $this->LabApi->respond_error('Product not found.', 404);
        return $product;
    }
    private function input(bool $partial = false): array
    {
        $result = $this->ProductInput->validate($this->LabApi->json_body(), $partial);
        if ($result['errors']) {
            $this->LabApi->respond(['error' => 'Please check the product information.', 'errors' => $result['errors']], 422);
        }
        return $result['data'];
    }
    public function index() { $this->LabApi->respond(['data' => $this->ProductModel->all()]); }
    public function show($id) { $this->LabApi->respond(['data' => $this->product($id)]); }
    public function create()
    {
        $id = $this->ProductModel->insert($this->input());
        if ($id === false) $this->LabApi->respond_error('Unable to save the product.', 500);
        $this->LabApi->respond(['message' => 'Product added successfully.', 'data' => $this->product($id)], 201);
    }
    public function update($id)
    {
        $this->product($id);
        $result = $this->ProductModel->update((int) $id, $this->input($_SERVER['REQUEST_METHOD'] === 'PATCH'));
        if ($result === false) $this->LabApi->respond_error('Unable to update the product.', 500);
        $this->LabApi->respond(['message' => 'Product updated successfully.', 'data' => $this->product($id)]);
    }
    public function delete($id)
    {
        $this->product($id);
        $result = $this->ProductModel->delete((int) $id);
        if ($result === false) $this->LabApi->respond_error('Unable to delete the product.', 500);
        $this->LabApi->respond(['message' => 'Product deleted successfully.']);
    }
}
