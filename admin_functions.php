<?php
session_start();
require_once 'db_connection.php';

// Check admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied");
}

// Function to delete a book and its chapters
function deleteBook($bookId) {
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete chapters first
        $stmt = $conn->prepare("DELETE FROM chapters WHERE book_id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        
        // Then delete the book
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error deleting book: " . $e->getMessage());
        return false;
    }
}

// Function to update user information
function updateUser($userId, $username, $email) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $email, $userId);
    return $stmt->execute();
}

// Function to delete a user
function deleteUser($userId) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    return $stmt->execute();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];
    
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'delete_book':
                    if (isset($_POST['book_id'])) {
                        $bookId = intval($_POST['book_id']);
                        if (deleteBook($bookId)) {
                            $response['success'] = true;
                            $response['message'] = 'Book deleted successfully';
                        } else {
                            $response['message'] = 'Failed to delete book';
                        }
                    }
                    break;
                    
                case 'update_user':
                    if (isset($_POST['user_id'], $_POST['username'], $_POST['email'])) {
                        $userId = intval($_POST['user_id']);
                        $username = trim($_POST['username']);
                        $email = trim($_POST['email']);
                        
                        if (empty($username) || empty($email)) {
                            $response['message'] = 'Username and email are required';
                        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $response['message'] = 'Invalid email format';
                        } else {
                            if (updateUser($userId, $username, $email)) {
                                $response['success'] = true;
                                $response['message'] = 'User updated successfully';
                            } else {
                                $response['message'] = 'Failed to update user';
                            }
                        }
                    }
                    break;
                    
                case 'delete_user':
                    if (isset($_POST['user_id'])) {
                        $userId = intval($_POST['user_id']);
                        if (deleteUser($userId)) {
                            $response['success'] = true;
                            $response['message'] = 'User deleted successfully';
                        } else {
                            $response['message'] = 'Failed to delete user';
                        }
                    }
                    break;
                    
                default:
                    $response['message'] = 'Invalid action';
            }
        } else {
            $response['message'] = 'No action specified';
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
        error_log("Admin function error: " . $e->getMessage());
    }
    
    echo json_encode($response);
    exit();
}

// Redirect if not AJAX
header("Location: admin_panel.php");
exit();
?>