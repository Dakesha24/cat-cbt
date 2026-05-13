<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::index');
$routes->get('guide', 'Home::guide');
$routes->get('profile', 'Home::profile');
$routes->get('contact', 'Home::contact');
$routes->get('about', 'Home::about');
$routes->get('faq', 'Home::faq');
$routes->get('bantuan', 'Home::bantuan');

// Auth routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');

// Admin routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
  $routes->get('dashboard', 'Admin::dashboard');

  // Kelola Guru
  $routes->get('guru', 'Admin::daftarGuru');
  $routes->get('guru/tambah', 'Admin::formTambahGuru');
  $routes->post('guru/tambah', 'Admin::tambahGuru');
  $routes->get('guru/edit/(:num)', 'Admin::formEditGuru/$1');
  $routes->post('guru/edit/(:num)', 'Admin::editGuru/$1');
  $routes->get('guru/hapus/(:num)', 'Admin::hapusGuru/$1');
  $routes->get('guru/restore/(:num)', 'Admin::restoreGuru/$1');
  $routes->post('guru/assign-kelas', 'Admin::assignKelas');
  $routes->get('guru/remove-kelas/(:num)/(:num)', 'Admin::removeKelas/$1/$2');

  // Kelola Siswa
  $routes->get('siswa', 'Admin::daftarSiswa');
  $routes->get('siswa/tambah', 'Admin::formTambahSiswa');
  $routes->post('siswa/tambah', 'Admin::tambahSiswa');
  $routes->get('siswa/edit/(:num)', 'Admin::formEditSiswa/$1');
  $routes->post('siswa/edit/(:num)', 'Admin::editSiswa/$1');
  $routes->get('siswa/hapus/(:num)', 'Admin::hapusSiswa/$1');
  $routes->get('siswa/restore/(:num)', 'Admin::restoreSiswa/$1');
  $routes->get('siswa/batch', 'Admin::batchCreateSiswa');

  // Kelola Sekolah
  $routes->get('sekolah', 'Admin::daftarSekolah');
  $routes->get('sekolah/tambah', 'Admin::formTambahSekolah');
  $routes->post('sekolah/tambah', 'Admin::tambahSekolah');
  $routes->get('sekolah/edit/(:num)', 'Admin::formEditSekolah/$1');
  $routes->post('sekolah/edit/(:num)', 'Admin::editSekolah/$1');
  $routes->get('sekolah/hapus/(:num)', 'Admin::hapusSekolah/$1');

  // Daftar Kelas dalam Sekolah
  $routes->get('sekolah/(:num)/kelas', 'Admin::daftarKelasBySekolah/$1');
  $routes->get('sekolah/(:num)/kelas/tambah', 'Admin::formTambahKelasSekolah/$1');
  $routes->post('sekolah/(:num)/kelas/tambah', 'Admin::tambahKelasSekolah/$1');

  $routes->get('sekolah/(:num)/kelas/edit/(:num)', 'Admin::formEditKelasSekolah/$1/$2');
  $routes->post('sekolah/(:num)/kelas/edit/(:num)', 'Admin::editKelasSekolah/$1/$2');
  $routes->get('sekolah/(:num)/kelas/hapus/(:num)', 'Admin::hapusKelasSekolah/$1/$2');

  $routes->get('sekolah/(:num)/kelas/(:num)/detail', 'Admin::detailKelasSekolah/$1/$2');
  $routes->post('sekolah/(:num)/kelas/(:num)/guru/assign', 'Admin::assignGuruKelasSekolah/$1/$2');
  $routes->get('sekolah/(:num)/kelas/(:num)/guru/remove/(:num)', 'Admin::removeGuruKelasSekolah/$1/$2/$3');
  $routes->get('sekolah/(:num)/kelas/(:num)/transfer-siswa/(:num)', 'Admin::transferSiswaSekolah/$1/$2/$3');
  $routes->post('sekolah/transfer-siswa/proses', 'Admin::prosesTransferSiswaSekolah');

  // Bank Soal
  $routes->get('bank-soal', 'Admin::bankSoal');
  $routes->post('bank-soal/tambah', 'Admin::tambahBankSoal');
  $routes->get('bank-soal/kategori/(:segment)', 'Admin::bankSoalKategori/$1');
  $routes->get('bank-soal/kategori/(:segment)/jenis-ujian/(:num)', 'Admin::bankSoalJenisUjian/$1/$2');
  $routes->get('bank-soal/kategori/(:segment)/jenis-ujian/(:num)/ujian/(:num)', 'Admin::bankSoalUjian/$1/$2/$3');
  $routes->post('bank-soal/tambah-soal', 'Admin::tambahSoalBankUjian');
  $routes->post('bank-soal/edit-soal/(:num)', 'Admin::editSoalBankUjian/$1');
  $routes->get('bank-soal/hapus-soal/(:num)', 'Admin::hapusSoalBankUjian/$1');
  $routes->get('bank-soal/hapus/(:num)', 'Admin::hapusBankUjian/$1');

  // Kelola Mata Pelajaran
  $routes->get('jenis-ujian', 'Admin::jenisUjian');
  $routes->post('jenis-ujian/tambah', 'Admin::tambahJenisUjian');
  $routes->post('jenis-ujian/edit/(:num)', 'Admin::editJenisUjian/$1');
  $routes->get('jenis-ujian/hapus/(:num)', 'Admin::hapusJenisUjian/$1');

  // API routes untuk AJAX
  $routes->get('api/kelas-by-sekolah/(:num)', 'Admin::getKelasBySekolah/$1');

  // API routes untuk AJAX
  $routes->get('bank-soal/api/kategori', 'Admin::getKategoriTersedia');
  $routes->get('bank-soal/api/jenis-ujian', 'Admin::getJenisUjianByKategori');
  $routes->get('bank-soal/api/bank-ujian', 'Admin::getBankUjianByKategoriJenis');
  $routes->get('bank-soal/api/soal', 'Admin::getSoalBankUjian');


  // Kelola Ujian
  $routes->get('ujian', 'Admin::ujian');
  $routes->post('ujian/tambah', 'Admin::tambahUjian');
  $routes->post('ujian/edit/(:num)', 'Admin::editUjian/$1');
  $routes->get('ujian/hapus/(:num)', 'Admin::hapusUjian/$1');

  // Kelola soal ujian

  $routes->get('soal/(:num)', 'Admin::kelolaSoal/$1');
  $routes->post('soal/tambah', 'Admin::tambahSoal');
  $routes->post('soal/edit/(:num)', 'Admin::editSoal/$1');
  $routes->get('soal/hapus/(:num)/(:num)', 'Admin::hapusSoal/$1/$2');
  $routes->post('soal/import-bank', 'Admin::importSoalDariBank');

  // Kelola Jadwal Ujian
  $routes->get('jadwal-ujian', 'Admin::jadwalUjian');
  $routes->post('jadwal-ujian/tambah', 'Admin::tambahJadwal');
  $routes->post('jadwal-ujian/edit/(:num)', 'Admin::editJadwal/$1');
  $routes->get('jadwal-ujian/hapus/(:num)', 'Admin::hapusJadwal/$1');

  // Kelola Hasil Ujian
  $routes->get('hasil-ujian', 'Admin::daftarHasilUjian');
  $routes->get('hasil-ujian/siswa/(:num)', 'Admin::hasilUjianSiswa/$1');
  $routes->get('hasil-ujian/detail/(:num)', 'Admin::detailHasilSiswa/$1');
  $routes->get('hasil-ujian/hapus/(:num)', 'Admin::hapusHasilSiswa/$1');

  $routes->get('hasil-ujian/download-excel/(:num)', 'Admin::downloadExcelHTML/$1');
  $routes->get('hasil-ujian/download-pdf/(:num)', 'Admin::downloadPDFHTML/$1');

  // Kelola Pengumuman
  $routes->get('pengumuman', 'Admin::daftarPengumuman');
  $routes->get('pengumuman/tambah', 'Admin::formTambahPengumuman');
  $routes->post('pengumuman/tambah', 'Admin::tambahPengumuman');
  $routes->get('pengumuman/edit/(:num)', 'Admin::formEditPengumuman/$1');
  $routes->post('pengumuman/edit/(:num)', 'Admin::editPengumuman/$1');
  $routes->get('pengumuman/detail/(:num)', 'Admin::detailPengumuman/$1');
  $routes->get('pengumuman/hapus/(:num)', 'Admin::hapusPengumuman/$1');
  $routes->get('pengumuman/toggle/(:num)', 'Admin::toggleStatusPengumuman/$1');

  // debugging (bisa dihps)
  $routes->get('debug-hasil', 'Admin::debugHasilUjian');
  $routes->get('update-status', 'Admin::updateStatusJadwal');

  //summernot untuk soal
  $routes->post('upload-ckeditor5-image', 'Admin::uploadCKEditor5Image');
  $routes->post('upload-summernote-image', 'Admin::uploadSummernoteImage');
  $routes->post('cleanup-temp-images', 'Admin::cleanupTempImages');
});

// Guru routes
$routes->group('guru', ['namespace' => 'App\Controllers\Guru'], function ($routes) {
  $routes->get('dashboard', 'Guru::dashboard');
  $routes->get('jenis-ujian', 'Guru::jenisUjian');
  $routes->get('ujian', 'Guru::ujian');
  $routes->get('jadwal-ujian', 'Guru::jadwalUjian');
  $routes->get('hasil-ujian', 'Guru::hasilUjian');
  $routes->get('pengumuman', 'Guru::pengumuman');

  $routes->post('jenis-ujian/tambah', 'Guru::tambahJenisUjian');
  $routes->post('jenis-ujian/edit/(:num)', 'Guru::editJenisUjian/$1');
  $routes->get('jenis-ujian/hapus/(:num)', 'Guru::hapusJenisUjian/$1');

  $routes->post('ujian/tambah', 'Guru::tambahUjian');
  $routes->post('ujian/edit/(:num)', 'Guru::editUjian/$1');
  $routes->get('ujian/hapus/(:num)', 'Guru::hapusUjian/$1');

  $routes->get('soal/(:num)', 'Guru::kelolaSoal/$1');
  $routes->post('soal/tambah', 'Guru::tambahSoal');
  $routes->post('soal/edit/(:num)', 'Guru::editSoal/$1');
  $routes->get('soal/hapus/(:num)/(:num)', 'Guru::hapusSoal/$1/$2');

  $routes->post('jadwal-ujian/tambah', 'Guru::tambahJadwal');
  $routes->post('jadwal-ujian/edit/(:num)', 'Guru::editJadwal/$1');
  $routes->get('jadwal-ujian/hapus/(:num)', 'Guru::hapusJadwal/$1');

  $routes->post('pengumuman/tambah', 'Guru::tambahPengumuman');
  $routes->post('pengumuman/edit/(:num)', 'Guru::editPengumuman/$1');
  $routes->get('pengumuman/hapus/(:num)', 'Guru::hapusPengumuman/$1');

  $routes->get('hasil-ujian', 'Guru::hasilUjian');
  $routes->get('hasil-ujian/siswa/(:num)', 'Guru::daftarSiswa/$1');
  $routes->get('hasil-ujian/detail/(:num)', 'Guru::detailHasil/$1');

  $routes->get('hasil-ujian/hapus/(:num)', 'Guru::hapusHasilSiswa/$1');
  $routes->get('hasil-ujian/reset/(:num)', 'Guru::resetStatusSiswa/$1');


  $routes->get('profil', 'Guru::profil');
  $routes->post('profil/save', 'Guru::saveProfil');

  $routes->get('bank-soal', 'Guru::bankSoal');
  $routes->post('bank-soal/tambah', 'Guru::tambahBankSoal');
  $routes->get('bank-soal/kategori/(:segment)', 'Guru::bankSoalKategori/$1');
  $routes->get('bank-soal/kategori/(:segment)/jenis-ujian/(:num)', 'Guru::bankSoalJenisUjian/$1/$2');
  $routes->get('bank-soal/kategori/(:segment)/jenis-ujian/(:num)/ujian/(:num)', 'Guru::bankSoalUjian/$1/$2/$3');

  $routes->get('bank-soal/api/jenis-ujian-kelas', 'Guru::getJenisUjianForKelas');

  // CRUD Soal Bank Ujian
  $routes->post('bank-soal/tambah-soal', 'Guru::tambahSoalBankUjian');
  $routes->post('bank-soal/edit-soal/(:num)', 'Guru::editSoalBankUjian/$1');
  $routes->get('bank-soal/hapus-soal/(:num)', 'Guru::hapusSoalBankUjian/$1');

  //kelola bank
  $routes->get('bank-soal/api/kategori', 'Guru::getKategoriTersedia');
  $routes->get('bank-soal/api/jenis-ujian', 'Guru::getJenisUjianByKategori');
  $routes->get('bank-soal/api/bank-ujian', 'Guru::getBankUjianByKategoriJenis');
  $routes->get('bank-soal/api/soal', 'Guru::getSoalBankUjian');
  $routes->post('soal/import-bank', 'Guru::importSoalDariBank');

  //summernot untuk soal
  $routes->post('upload-ckeditor5-image', 'Guru::uploadCKEditor5Image');
  $routes->post('upload-summernote-image', 'Guru::uploadSummernoteImage');
  $routes->post('cleanup-temp-images', 'Guru::cleanupTempImages');
});

$routes->get('guru/hasil-ujian/download-excel-html/(:num)', 'Guru\Guru::downloadExcelHTML/$1');
$routes->get('guru/hasil-ujian/download-pdf-html/(:num)', 'Guru\Guru::downloadPDFHTML/$1');


// Siswa routes
$routes->group('siswa', ['namespace' => 'App\Controllers\Siswa'], function ($routes) {
  $routes->get('dashboard', 'Siswa::dashboard');
  $routes->get('pengumuman', 'Siswa::pengumuman');
  $routes->get('ujian', 'Siswa::ujian');
  $routes->get('hasil', 'Siswa::hasil');
  $routes->get('hasil/detail/(:num)', 'Siswa::detailHasil/$1');
  $routes->get('profil', 'Siswa::profil');
  $routes->post('profil/save', 'Siswa::saveProfil');
  $routes->post('ujian/mulai', 'Siswa::mulaiUjian');
  $routes->get('ujian/soal/(:num)', 'Siswa::soal/$1');
  $routes->get('ujian/selesai/(:num)', 'Siswa::selesaiUjian/$1');
  $routes->get('hasil/review/(:num)', 'Siswa::review/$1');
  $routes->post('ujian/simpan-jawaban', 'Siswa::simpanJawaban');
  $routes->post('ujian/mulai', 'Siswa::mulaiUjian');
  $routes->get('ujian/soal/(:num)', 'Siswa::soal/$1');

  $routes->get('hasil/unduh/(:num)', 'Siswa::unduh/$1');

  $routes->get('api/kelas-by-sekolah/(:num)', 'Siswa::getKelasBySekolah/$1');

  $routes->get('hasil/unduh/(:num)', 'Siswa::unduh/$1');
});
