<?php
require_once __DIR__ . '/../config/Database.php';

class DetailStokController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function readAll() {
        $query = "SELECT ds.*, sb.nama_bahan, u.nama as nama_petugas 
                  FROM detail_stok ds
                  JOIN stok_bahan sb ON ds.id_stok = sb.id_stok
                  JOIN users u ON ds.id_users = u.id_users
                  ORDER BY ds.id_detail_stok DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($id_stok, $jenis_transaksi, $jumlah, $keterangan, $id_users) {
        try {
            $query = "INSERT INTO detail_stok (id_stok, jenis_transaksi, jumlah, keterangan, id_users) 
                      VALUES (:id_stok, :jenis_transaksi, :jumlah, :keterangan, :id_users)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id_stok' => $id_stok,
                ':jenis_transaksi' => $jenis_transaksi,
                ':jumlah' => $jumlah,
                ':keterangan' => $keterangan,
                ':id_users' => $id_users
            ]);
            return ['status' => 'success', 'msg' => 'Transaksi mutasi berhasil dibukukan!'];
        } catch (PDOException $e) {
            // Menangkap pesan error dari Trigger MySQL (Misal: Pencegahan Stok Minus)
            return ['status' => 'danger', 'msg' => $e->getMessage()];
        }
    }
}
?>