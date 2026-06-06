<?= $this->extend('templates/admin/admin_template') ?>
<?= $this->section('title') ?>Detail Hasil Ujian<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $detailRole = 'admin'; ?>
<?= $this->include('shared/detail_hasil_modern') ?>
<?= $this->endSection() ?>
