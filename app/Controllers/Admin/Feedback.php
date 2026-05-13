<?php
namespace App\Controllers\Admin;

use CodeIgniter\Controller;

class Feedback extends Controller
{
    protected $feedbackModel;

    public function __construct()
    {
        // Check if user is admin
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'));
        }
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        $date = $this->request->getGet('date');
        $search = $this->request->getGet('search');

        $query = $this->feedbackModel;

        // Apply filters
        if ($status) {
            $query = $query->where('status', $status);
        }
        if ($date) {
            $query = $query->where('DATE(created_at)', $date);
        }
        if ($search) {
            $query = $query->like('name', $search)
                          ->orLike('email', $search)
                          ->orLike('message', $search);
        }

        $data['feedbacks'] = $query->orderBy('created_at', 'DESC')
                                  ->paginate(10);
        $data['pager'] = $this->feedbackModel->pager;

        return view('admin/feedback', $data);
    }

    public function markRead($id)
    {
        $this->feedbackModel->update($id, ['status' => 'read']);
        return $this->response->setJSON(['success' => true]);
    }

    public function markAllRead()
    {
        $this->feedbackModel->where('status', 'unread')
                           ->set(['status' => 'read'])
                           ->update();
        return $this->response->setJSON(['success' => true]);
    }

    public function delete($id)
    {
        $this->feedbackModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }

    public function deleteSelected()
    {
        $ids = $this->request->getPost('ids');
        if (!empty($ids)) {
            $this->feedbackModel->whereIn('id', $ids)->delete();
        }
        return $this->response->setJSON(['success' => true]);
    }
}