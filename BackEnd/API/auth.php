<?php
include '../includes/config.php';

class Auth {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($data) {
        try {
            // Check if email exists
            $query = "SELECT user_id FROM " . $this->table . " WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$data['email']]);
            
            if($stmt->rowCount() > 0) {
                return ["success" => false, "message" => "Email already registered"];
            }

            // Hash password
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insert user
            $query = "INSERT INTO " . $this->table . " 
                     (name, email, password, student_id, phone) 
                     VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $data['name'],
                $data['email'],
                $hashed_password,
                $data['student_id'],
                $data['phone']
            ]);

            return ["success" => true, "message" => "Registration successful"];

        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

    public function login($email, $password) {
        try {
            $query = "SELECT user_id, name, email, password, role FROM " . $this->table . " WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            
            if($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    return [
                        "success" => true, 
                        "message" => "Login successful",
                        "user" => [
                            "id" => $user['user_id'],
                            "name" => $user['name'],
                            "email" => $user['email'],
                            "role" => $user['role']
                        ]
                    ];
                }
            }
            
            return ["success" => false, "message" => "Invalid credentials"];
            
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

    public function checkAuth() {
        if(isset($_SESSION['user_id'])) {
            return [
                "authenticated" => true,
                "user" => [
                    "id" => $_SESSION['user_id'],
                    "name" => $_SESSION['name'],
                    "email" => $_SESSION['email'],
                    "role" => $_SESSION['role']
                ]
            ];
        }
        return ["authenticated" => false];
    }

    public function logout() {
        session_destroy();
        return ["success" => true, "message" => "Logged out successfully"];
    }
}

// Handle requests
$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch($method) {
    case 'POST':
        if($action == 'register') {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $auth->register($data);
            echo json_encode($result);
        }
        elseif($action == 'login') {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $auth->login($data['email'], $data['password']);
            echo json_encode($result);
        }
        break;
        
    case 'GET':
        if($action == 'check') {
            $result = $auth->checkAuth();
            echo json_encode($result);
        }
        break;
}
?>