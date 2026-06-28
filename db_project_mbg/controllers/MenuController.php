<?php
require_once __DIR__ . '/../config/Database.php';

class MenuController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function readAll() {
        $query = "SELECT * FROM menu_makanan ORDER BY id_menu DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nama, $kalori, $protein, $status) {
        $query = "INSERT INTO menu_makanan (nama_menu, kalori, protein, status) VALUES (:nama, :kalori, :protein, :status)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':nama' => $nama,
            ':kalori' => $kalori,
            ':protein' => $protein,
            ':status' => $status
        ]);
        return ['status' => 'success', 'msg' => 'Varian paket menu berhasil ditambahkan!'];
    }

    public function update($id, $nama, $kalori, $protein, $status) {
        $query = "UPDATE menu_makanan SET nama_menu = :nama, kalori = :kalori, protein = :protein, status = :status WHERE id_menu = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id' => $id,
            ':nama' => $nama,
            ':kalori' => $kalori,
            ':protein' => $protein,
            ':status' => $status
        ]);
        return ['status' => 'success', 'msg' => 'Data menu makanan diperbarui!'];
    }

    public function delete($id) {
        $query = "DELETE FROM menu_makanan WHERE id_menu = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return ['status' => 'success', 'msg' => 'Paket menu makanan berhasil dihapus!'];
    }
}
?>