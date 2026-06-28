<?php
require_once __DIR__ . '/../config/Database.php';

class DistribusiController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function readAll() {
        // Memastikan kolom status ikut terpanggil jika ada di tabel distribusi
        $query = "SELECT d.*, s.nama_sekolah, m.nama_menu, u.nama as nama_petugas 
                  FROM distribusi d
                  JOIN sekolah s ON d.id_sekolah = s.id_sekolah
                  JOIN menu_makanan m ON d.id_menu = m.id_menu
                  JOIN users u ON d.id_users = u.id_users
                  ORDER BY d.tanggal_distribusi DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    // DISESUAIKAN: Mengubah $id_users menjadi $id_user agar cocok dengan $_POST['id_user'] dari view
    public function create($id_sekolah, $id_menu, $tanggal_distribusi, $jumlah_porsi, $id_user) {
        $query = "INSERT INTO distribusi (id_sekolah, id_menu, tanggal_distribusi, jumlah_porsi, id_users, status) 
                  VALUES (:id_sekolah, :id_menu, :tanggal_distribusi, :jumlah_porsi, :id_users, 'proses')";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_sekolah' => $id_sekolah,
            ':id_menu' => $id_menu,
            ':tanggal_distribusi' => $tanggal_distribusi,
            ':jumlah_porsi' => $jumlah_porsi,
            ':id_users' => $id_user
        ]);
        return ['status' => 'success', 'msg' => 'Data distribusi berhasil dicatatkan!'];
    }

    /**
     * PERBAIKAN: Menambahkan parameter $status dan memperbarui query UPDATE
     * agar perubahan status dari modal edit tersimpan ke database.
     */
    public function update($id_distribusi, $id_sekolah, $id_menu, $tanggal_distribusi, $jumlah_porsi, $id_user, $status) {
        try {
            $query = "UPDATE distribusi 
                      SET id_sekolah = :id_sekolah, 
                          id_menu = :id_menu, 
                          tanggal_distribusi = :tanggal_distribusi, 
                          jumlah_porsi = :jumlah_porsi, 
                          id_users = :id_users,
                          status = :status 
                      WHERE id_distribusi = :id_distribusi";
                      
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id_distribusi'       => $id_distribusi,
                ':id_sekolah'          => $id_sekolah,
                ':id_menu'             => $id_menu,
                ':tanggal_distribusi'  => $tanggal_distribusi,
                ':jumlah_porsi'        => $jumlah_porsi,
                ':id_users'            => $id_user,
                ':status'              => $status
            ]);
            return ['status' => 'success', 'msg' => 'Data log distribusi dan status berhasil diperbarui!'];
        } catch (PDOException $e) {
            return ['status' => 'danger', 'msg' => 'Gagal memperbarui data: ' . $e->getMessage()];
        }
    }

    public function delete($id_distribusi) {
        $query = "DELETE FROM distribusi WHERE id_distribusi = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id_distribusi]);
        return ['status' => 'success', 'msg' => 'Data distribusi berhasil dihapus.'];
    }

    /**
     * METHOD BARU: Mengubah status distribusi menjadi 'selesai'
     * Dipanggil saat tombol Selesai di klik di file utama
     */
    public function complete($id_distribusi) {
        try {
            $query = "UPDATE distribusi SET status = 'selesai' WHERE id_distribusi = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id_distribusi]);
            
            return [
                'status' => 'success', 
                'msg' => 'Pengiriman berhasil diselesaikan! Status manifest telah diperbarui.'
            ];
        } catch (PDOException $e) {
            return [
                'status' => 'danger', 
                'msg' => 'Gagal memperbarui status pengiriman: ' . $e->getMessage()
            ];
        }
    }
}
?>