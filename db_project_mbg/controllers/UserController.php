<?php
require_once __DIR__ . '/../config/Database.php';

class UserController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Mendapatkan semua data user
    public function readAll() {
        $query = "SELECT id_users, nama, username, role, created_at FROM users ORDER BY id_users DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menambah user baru dengan password yang di-hash
    public function create($nama, $username, $password, $role) {
        // Validasi jika username sudah digunakan
        $checkQuery = "SELECT id_users FROM users WHERE username = :username LIMIT 1";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([':username' => $username]);
        
        if ($checkStmt->rowCount() > 0) {
            return ['status' => 'danger', 'msg' => 'Username sudah terdaftar! Gunakan username lain.'];
        }

        // Enkripsi password menggunakan bcrypt standar PHP yang aman
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (nama, username, password, role, created_at) 
                  VALUES (:nama, :username, :password, :role, NOW())";
        $stmt = $this->db->prepare($query);
        
        try {
            $stmt->execute([
                ':nama' => $nama,
                ':username' => $username,
                ':password' => $hashed_password,
                ':role' => $role
            ]);
            return ['status' => 'success', 'msg' => 'Pengguna baru berhasil ditambahkan!'];
        } catch (PDOException $e) {
            return ['status' => 'danger', 'msg' => 'Gagal menambah pengguna: ' . $e->getMessage()];
        }
    }

    // Memperbarui data user (Password opsional)
    public function update($id_users, $nama, $username, $password, $role) {
        try {
            // Jika password diisi, ikut perbarui password dengan enkripsi baru
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $query = "UPDATE users SET nama = :nama, username = :username, password = :password, role = :role WHERE id_users = :id_users";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':password', $hashed_password);
            } else {
                // Jika password dikosongkan, perbarui data selain password
                $query = "UPDATE users SET nama = :nama, username = :username, role = :role WHERE id_users = :id_users";
                $stmt = $this->db->prepare($query);
            }

            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id_users', $id_users);
            
            $stmt->execute();
            return ['status' => 'success', 'msg' => 'Data pengguna berhasil diperbarui!'];
        } catch (PDOException $e) {
            return ['status' => 'danger', 'msg' => 'Gagal memperbarui data: ' . $e->getMessage()];
        }
    }

    // Menghapus user
    public function delete($id_users, $current_session_id) {
        // Cegah user menghapus akunnya sendiri yang sedang login
        if ($id_users == $current_session_id) {
            return ['status' => 'danger', 'msg' => 'Gagal: Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!'];
        }

        $query = "DELETE FROM users WHERE id_users = :id";
        $stmt = $this->db->prepare($query);
        
        try {
            $stmt->execute([':id' => $id_users]);
            return ['status' => 'success', 'msg' => 'Pengguna berhasil dihapus dari sistem.'];
        } catch (PDOException $e) {
            return ['status' => 'danger', 'msg' => 'Gagal menghapus pengguna: ' . $e->getMessage()];
        }
    }
}
?>