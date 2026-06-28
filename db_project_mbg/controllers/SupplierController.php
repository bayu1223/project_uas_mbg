<?php
require_once __DIR__ . '/../config/Database.php';

class SupplierController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function readAll() {
        $query = "SELECT * FROM supplier ORDER BY id_supplier DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nama, $alamat, $no_telp, $email) {
        $query = "INSERT INTO supplier (nama_supplier, alamat, no_telp, email) VALUES (:nama, :alamat, :no_telp, :email)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':nama' => $nama,
            ':alamat' => $alamat,
            ':no_telp' => $no_telp,
            ':email' => $email
        ]);
        return ['status' => 'success', 'msg' => 'Data supplier berhasil ditambahkan!'];
    }

    public function update($id, $nama, $alamat, $no_telp, $email) {
        $query = "UPDATE supplier SET nama_supplier = :nama, alamat = :alamat, no_telp = :no_telp, email = :email WHERE id_supplier = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id' => $id,
            ':nama' => $nama,
            ':alamat' => $alamat,
            ':no_telp' => $no_telp,
            ':email' => $email
        ]);
        return ['status' => 'success', 'msg' => 'Data supplier berhasil diperbarui!'];
    }

    public function delete($id) {
        $query = "DELETE FROM supplier WHERE id_supplier = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return ['status' => 'success', 'msg' => 'Data supplier berhasil dihapus!'];
    }
}
?>