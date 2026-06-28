<?php
require_once __DIR__ . '/../config/Database.php';

class DashboardController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getSummary() {
        $summary = [];
        $summary['total_sekolah'] = $this->db->query("SELECT COUNT(*) FROM sekolah")->fetchColumn();
        $summary['total_supplier'] = $this->db->query("SELECT COUNT(*) FROM supplier")->fetchColumn();
        $summary['total_menu'] = $this->db->query("SELECT COUNT(*) FROM menu_makanan WHERE status='aktif'")->fetchColumn();
        
        // Log transaksi stok teranyar menggunakan JOIN internal
        $queryLog = "SELECT ds.*, sb.nama_bahan, u.nama as nama_user 
                     FROM detail_stok ds
                     JOIN stok_bahan sb ON ds.id_stok = sb.id_stok
                     JOIN users u ON ds.id_users = u.id_users
                     ORDER BY ds.tanggal_transaksi DESC LIMIT 5";
        $summary['recent_logs'] = $this->db->query($queryLog)->fetchAll(PDO::FETCH_ASSOC);
        
        return $summary;
    }
}
?>