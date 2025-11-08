<?php
include '../includes/config.php';

class Events {
    private $conn;
    private $table = "events";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getEvents() {
        try {
            $query = "SELECT e.*, u.name as organizer_name, 
                             COUNT(r.reg_id) as registered_count
                      FROM " . $this->table . " e 
                      LEFT JOIN users u ON e.created_by = u.user_id 
                      LEFT JOIN registrations r ON e.event_id = r.event_id 
                      WHERE e.status = 'active'
                      GROUP BY e.event_id 
                      ORDER BY e.date ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $events = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $events[] = $row;
            }
            
            return ["success" => true, "events" => $events];
            
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }
}

// Handle requests
$database = new Database();
$db = $database->getConnection();
$events = new Events($db);

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET') {
    $result = $events->getEvents();
    echo json_encode($result);
}
?>