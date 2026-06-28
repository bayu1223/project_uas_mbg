<?php
require_once __DIR__ . '/../config/Database.php';

class StokBahanController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function readAll() {
        // Menggunakan JOIN untuk menampilkan nama_supplier, bukan sekadar ID mentah
        $query = "SELECT sb.*, s.nama_supplier 
                  FROM stok_bahan sb
                  LEFT JOIN supplier s ON sb.id_supplier = s.id_supplier
                  ORDER BY sb.id_stok DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nama_bahan, $jumlah, $satuan, $id_supplier) {
        $query = "INSERT INTO stok_bahan (nama_bahan, jumlah, satuan, id_supplier, tanggal_update) 
                  VALUES (:nama_bahan, :jumlah, :satuan, :id_supplier, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':nama_bahan' => $nama_bahan,
            ':jumlah' => $jumlah,
            ':satuan' => $satuan,
            ':id_supplier' => $id_supplier
        ]);
        return ['status' => 'success', 'msg' => 'Data stok bahan baku berhasil ditambahkan!'];
    }

    public function update($id, $nama_bahan, $jumlah, $satuan, $id_supplier) {
        $query = "UPDATE stok_bahan 
                  SET nama_bahan = :nama_bahan, jumlah = :jumlah, satuan = :satuan, id_supplier = :id_supplier, tanggal_update = NOW() 
                  WHERE id_stok = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id' => $id,
            ':nama_bahan' => $nama_bahan,
            ':jumlah' => $jumlah,
            ':satuan' => $satuan,
            ':id_supplier' => $id_supplier
        ]);
        return ['status' => 'success', 'msg' => 'Data stok bahan berhasil diperbarui!'];
    }

    public function delete($id) {
        $query = "DELETE FROM stok_bahan WHERE id_stok = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return ['status' => 'success', 'msg' => 'Data stok bahan baku berhasil dihapus!'];
    }
}
?>