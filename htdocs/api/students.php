<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$pdo = new PDO("mysql:host=localhost;dbname=pbo_api", "root", "");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);
$pathInfo = $_SERVER['PATH_INFO'] ?? '';          // contoh: "/students/2"
$segments = array_values(array_filter(explode('/', trim($pathInfo, '/'))));
// $segments[0] = "students", $segments[1] = "2" (kalau ada)

$pathId = $segments[1] ?? null;

// fallback: kalau tidak lewat path, coba cek query string ?id=2
$id = $pathId ?? ($_GET['id'] ?? null);

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            $stmt = $pdo->query("SELECT * FROM students");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        $stmt = $pdo->prepare("INSERT INTO students (nim, name, major) VALUES (?, ?, ?)");
        $stmt->execute([$input['nim'], $input['name'], $input['major']]);
        echo json_encode(['message' => 'Data mahasiswa berhasil ditambahkan']);
        break;

    case 'PUT':
        $stmt = $pdo->prepare("UPDATE students SET nim=?, name=?, major=? WHERE id=?");
        $stmt->execute([$input['nim'], $input['name'], $input['major'], $id]);
        echo json_encode(['message' => 'Data mahasiswa berhasil diubah']);
        break;

    case 'DELETE':
        $stmt = $pdo->prepare("DELETE FROM students WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Data mahasiswa berhasil dihapus']);
        break;
}
?>
