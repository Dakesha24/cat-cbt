<?php
$tipeLabels = ['text'=>'Text','number'=>'Angka','select'=>'Pilihan','date'=>'Tanggal','textarea'=>'Teks Panjang'];

$fieldJson = htmlspecialchars(json_encode([
    'label'    => $field['label'],
    'tipe'     => $field['tipe'],
    'tipe_raw' => $field['tipe'],
    'options'  => array_column($field['options'] ?? [], 'label'),
]), ENT_QUOTES);
?>
<div class="fb-row" id="field-<?= (int)$field['field_id'] ?>"
     data-id="<?= (int)$field['field_id'] ?>"
     data-label="<?= esc($field['label']) ?>"
     data-tipe="<?= esc($field['tipe']) ?>"
     data-placeholder="<?= esc($field['placeholder'] ?? '') ?>"
     data-required="<?= (int)$field['is_required'] ?>"
     data-field-json="<?= $fieldJson ?>">

    <?php foreach ($field['options'] ?? [] as $opt): ?>
        <span data-option-id="<?= (int)$opt['option_id'] ?>" style="display:none"><?= esc($opt['label']) ?></span>
    <?php endforeach; ?>

    <div class="fb-handle" title="Ubah urutan"></div>
    <span class="fb-num"><?= $nomor ?? '' ?></span>

    <div class="fb-info">
        <div class="fb-name"><?= esc($field['label']) ?></div>
        <div class="fb-sub">
            <?= $tipeLabels[$field['tipe']] ?? esc($field['tipe']) ?>
            &middot;
            <?= $field['is_required'] ? 'Wajib' : 'Opsional' ?>
        </div>
    </div>

    <div class="fb-actions">
        <button class="fb-act" title="Pratinjau" onclick="showFieldPreview(<?= (int)$field['field_id'] ?>)">
            <i class="bi bi-eye"></i>
        </button>
        <button class="fb-act" title="Edit" onclick="editField(<?= (int)$field['field_id'] ?>)">
            <i class="bi bi-pencil"></i>
        </button>
        <button class="fb-act danger" title="Hapus" onclick="hapusField(<?= (int)$field['field_id'] ?>)">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</div>
