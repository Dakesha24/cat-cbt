<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FormBuilderSeeder extends Seeder
{
    public function run(): void
    {
        $db = \Config\Database::connect();

        // Ambil atau buat template tunggal
        $template = $db->table('form_templates')->get()->getRowArray();

        if (!$template) {
            $db->table('form_templates')->insert([
                'nama'       => 'Biodata Tambahan',
                'deskripsi'  => null,
                'is_active'  => 1,
                'created_by' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $templateId = $db->insertID();
        } else {
            $templateId = (int) $template['template_id'];
        }

        // Hapus field lama jika ada (seeder bersih)
        $existingFields = $db->table('form_fields')
            ->where('template_id', $templateId)
            ->get()->getResultArray();

        foreach ($existingFields as $f) {
            $db->table('form_field_options')->where('field_id', $f['field_id'])->delete();
        }
        $db->table('form_fields')->where('template_id', $templateId)->delete();

        // Definisi field
        $fields = [
            [
                'label'       => 'Tanggal Lahir',
                'tipe'        => 'date',
                'placeholder' => '',
                'is_required' => 1,
                'urutan'      => 1,
                'options'     => [],
            ],
            [
                'label'       => 'Tempat Lahir',
                'tipe'        => 'text',
                'placeholder' => 'Contoh: Jakarta',
                'is_required' => 1,
                'urutan'      => 2,
                'options'     => [],
            ],
            [
                'label'       => 'Provinsi',
                'tipe'        => 'select',
                'placeholder' => '',
                'is_required' => 1,
                'urutan'      => 3,
                'options'     => [
                    'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau',
                    'Jambi', 'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Bengkulu',
                    'Lampung', 'DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Tengah',
                    'DI Yogyakarta', 'Jawa Timur', 'Bali', 'Nusa Tenggara Barat',
                    'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah',
                    'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
                    'Sulawesi Utara', 'Gorontalo', 'Sulawesi Tengah', 'Sulawesi Barat',
                    'Sulawesi Selatan', 'Sulawesi Tenggara', 'Maluku', 'Maluku Utara',
                    'Papua', 'Papua Barat', 'Papua Selatan', 'Papua Tengah',
                    'Papua Pegunungan', 'Papua Barat Daya',
                ],
            ],
            [
                'label'       => 'Kabupaten / Kota',
                'tipe'        => 'text',
                'placeholder' => 'Contoh: Kota Bandung',
                'is_required' => 1,
                'urutan'      => 4,
                'options'     => [],
            ],
            [
                'label'       => 'Alamat Lengkap',
                'tipe'        => 'textarea',
                'placeholder' => 'Jl. ..., RT/RW ..., Kelurahan ...',
                'is_required' => 0,
                'urutan'      => 5,
                'options'     => [],
            ],
            [
                'label'       => 'Agama',
                'tipe'        => 'select',
                'placeholder' => '',
                'is_required' => 1,
                'urutan'      => 6,
                'options'     => [
                    'Islam', 'Kristen Protestan', 'Kristen Katolik',
                    'Hindu', 'Buddha', 'Konghucu',
                ],
            ],
            [
                'label'       => 'No. HP / WhatsApp',
                'tipe'        => 'text',
                'placeholder' => 'Contoh: 08123456789',
                'is_required' => 0,
                'urutan'      => 7,
                'options'     => [],
            ],
            [
                'label'       => 'Nama Orang Tua / Wali',
                'tipe'        => 'text',
                'placeholder' => 'Nama lengkap orang tua atau wali',
                'is_required' => 0,
                'urutan'      => 8,
                'options'     => [],
            ],
            [
                'label'       => 'No. HP Orang Tua / Wali',
                'tipe'        => 'text',
                'placeholder' => 'Contoh: 08129876543',
                'is_required' => 0,
                'urutan'      => 9,
                'options'     => [],
            ],
            [
                'label'       => 'Asal Sekolah / Instansi',
                'tipe'        => 'text',
                'placeholder' => 'Nama sekolah atau instansi sebelumnya',
                'is_required' => 0,
                'urutan'      => 10,
                'options'     => [],
            ],
        ];

        // Insert field dan opsinya
        foreach ($fields as $f) {
            $db->table('form_fields')->insert([
                'template_id' => $templateId,
                'label'       => $f['label'],
                'tipe'        => $f['tipe'],
                'placeholder' => $f['placeholder'],
                'is_required' => $f['is_required'],
                'urutan'      => $f['urutan'],
            ]);

            $fieldId = $db->insertID();

            foreach ($f['options'] as $i => $opsiLabel) {
                $db->table('form_field_options')->insert([
                    'field_id' => $fieldId,
                    'label'    => $opsiLabel,
                    'urutan'   => $i + 1,
                ]);
            }
        }

        echo "FormBuilderSeeder: " . count($fields) . " field berhasil ditambahkan.\n";
    }
}
