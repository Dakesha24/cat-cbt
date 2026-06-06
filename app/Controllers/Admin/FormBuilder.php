<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use App\Models\FormTemplateModel;
use App\Models\FormFieldModel;
use App\Models\FormFieldOptionModel;

class FormBuilder extends Controller
{
    private FormTemplateModel    $templateModel;
    private FormFieldModel       $fieldModel;
    private FormFieldOptionModel $optionModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);

        $this->templateModel = new FormTemplateModel();
        $this->fieldModel    = new FormFieldModel();
        $this->optionModel   = new FormFieldOptionModel();
    }

    // ── Halaman utama: kelola field biodata tambahan ────────────────
    public function index()
    {
        $template = $this->templateModel->getSingle();

        return view('admin/form_builder/index', [
            'template' => $template,
            'fields'   => $this->fieldModel->getWithOptions((int) $template['template_id']),
        ]);
    }

    // ── AJAX: Simpan field (tambah atau edit) ───────────────────────
    public function saveField()
    {
        $template   = $this->templateModel->getSingle();
        $templateId = (int) $template['template_id'];
        $fieldId    = (int) $this->request->getPost('field_id');
        $label      = trim((string) $this->request->getPost('label'));
        $tipe       = $this->request->getPost('tipe');
        $placeholder = $this->request->getPost('placeholder') ?? null;
        $isRequired  = (int) $this->request->getPost('is_required');

        if (!$label || !in_array($tipe, ['text', 'number', 'select', 'date', 'textarea'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak valid.']);
        }

        if ($fieldId > 0) {
            $this->fieldModel->update($fieldId, [
                'label'       => $label,
                'tipe'        => $tipe,
                'placeholder' => $placeholder,
                'is_required' => $isRequired,
            ]);
        } else {
            $fieldId = (int) $this->fieldModel->insert([
                'template_id' => $templateId,
                'label'       => $label,
                'tipe'        => $tipe,
                'placeholder' => $placeholder,
                'is_required' => $isRequired,
                'urutan'      => $this->fieldModel->getNextUrutan($templateId),
            ], true);
        }

        // Simpan opsi pending (hanya untuk field baru bertipe select)
        if ($tipe === 'select') {
            $pendingJson = $this->request->getPost('pending_options');
            if ($pendingJson) {
                $pendingLabels = json_decode($pendingJson, true) ?? [];
                foreach ($pendingLabels as $i => $label) {
                    if (trim($label) !== '') {
                        $this->optionModel->insert([
                            'field_id' => $fieldId,
                            'label'    => trim($label),
                            'urutan'   => $i + 1,
                        ]);
                    }
                }
            }
        }

        $field            = $this->fieldModel->find($fieldId);
        $field['options'] = $tipe === 'select' ? $this->optionModel->getByField($fieldId) : [];

        return $this->response->setJSON(['success' => true, 'field' => $field]);
    }

    // ── AJAX: Hapus field ───────────────────────────────────────────
    public function hapusField(int $fieldId)
    {
        $this->fieldModel->delete($fieldId);
        return $this->response->setJSON(['success' => true]);
    }

    // ── AJAX: Update urutan field ───────────────────────────────────
    public function updateUrutan()
    {
        $urutan = $this->request->getPost('urutan');

        if (!is_array($urutan)) {
            return $this->response->setJSON(['success' => false]);
        }

        foreach ($urutan as $index => $fieldId) {
            $this->fieldModel->update((int) $fieldId, ['urutan' => $index + 1]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    // ── AJAX: Simpan opsi ───────────────────────────────────────────
    public function saveOpsi()
    {
        $fieldId  = (int) $this->request->getPost('field_id');
        $label    = trim((string) $this->request->getPost('label'));
        $optionId = (int) $this->request->getPost('option_id');

        if (!$label) {
            return $this->response->setJSON(['success' => false, 'message' => 'Label opsi tidak boleh kosong.']);
        }

        if ($optionId > 0) {
            $this->optionModel->update($optionId, ['label' => $label]);
        } else {
            $optionId = (int) $this->optionModel->insert([
                'field_id' => $fieldId,
                'label'    => $label,
                'urutan'   => $this->optionModel->getNextUrutan($fieldId),
            ], true);
        }

        return $this->response->setJSON(['success' => true, 'option' => $this->optionModel->find($optionId)]);
    }

    // ── AJAX: Hapus opsi ────────────────────────────────────────────
    public function hapusOpsi(int $optionId)
    {
        $this->optionModel->delete($optionId);
        return $this->response->setJSON(['success' => true]);
    }
}
