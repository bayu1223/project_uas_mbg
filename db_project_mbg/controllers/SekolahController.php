<?php
require_once __DIR__ . '/../config/Database.php';

class SekolahController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function readAll() {
        $query = "SELECT * FROM sekolah ORDER BY id_sekolah DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nama, $alamat, $kepsek, $siswa, $no_telp) {
        $query = "INSERT INTO sekolah (nama_sekolah, alamat, kepala_sekolah, jumlah_siswa, no_telp) VALUES (:nama, :alamat, :kepsek, :siswa, :no_telp)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':nama' => $nama,
            ':alamat' => $alamat,
            ':kepsek' => $kepsek,
            ':siswa' => $siswa,
            ':no_telp' => $no_telp
        ]);
        return ['status' => 'success', 'msg' => 'Data sekolah berhasil diregistrasi!'];
    }

    public function update($id, $nama, $alamat, $kepsek, $siswa, $no_telp) {
        $query = "UPDATE sekolah SET nama_sekolah = :nama, alamat = :alamat, kepala_sekolah = :kepsek, jumlah_siswa = :siswa, no_telp = :no_telp WHERE id_sekolah = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id' => $id,
            ':nama' => $nama,
            ':alamat' => $alamat,
            ':kepsek' => $kepsek,
            ':siswa' => $siswa,
            ':no_telp' => $no_telp
        ]);
        return ['status' => 'success', 'msg' => 'Data sekolah berhasil diperbarui!'];
    }

    public function delete($id) {
        $query = "DELETE FROM sekolah WHERE id_sekolah = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return ['status' => 'success', 'msg' => 'Data sekolah berhasil dihapus!'];
    }
}
?>