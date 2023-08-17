<?php
include_once 'connect.php';
function addUniversity($universityName)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Universities (university_name) VALUES (?)");
    $stmt->execute([$universityName]);
}

function insertUser($username, $email, $password, $university_id, $specialization_id,$pdo) {
    
    // check if user with provided email already exists
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email=:email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        // user with provided email already exists, so return false
          echo 0;

    }
else{
        $password = password_hash($password, PASSWORD_DEFAULT);

    // user with provided email doesn't exist, so insert new user into database
    $stmt = $pdo->prepare("INSERT INTO Users (username, email, password, university_id, specialization_id, verification_code) VALUES (:username, :email, :password, :university_id, :specialization_id, :verification_code)");
    $verification_code = rand(10000, 99999); // generate a 6-digit verification code
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":university_id", $university_id);
    $stmt->bindParam(":specialization_id", $specialization_id);
    $stmt->bindParam(":verification_code", $verification_code);
    $stmt->execute();
    $count = $stmt->rowCount();

    if ($count > 0) {
        $to = $email;
    $subject = "Verify your account";
    $message = "Hello $username, your verification code is: $verification_code";
    $headers = "From: noreply@sync-fast.com" . "\r\n" . "Reply-To: admin@sync-fast.com" . "\r\n" . "X-Mailer: PHP/" . phpversion();
    mail($to, $subject, $message, $headers);
   echo 1;
    } else {
    echo 0;
     }
}
    // send verification email to user with verification code


    // user was successfully inserted into database, so return true
   
}
function login($usernameOrEmail, $password)
{
    global $pdo;

    // Check if the username or email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ? OR email = ?");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify the user's password
        if (password_verify($password, $user['password'])) {
            // Check if the user's email has been verified
            if ($user['verified']) {
                return $user;
            } else {
                return 'Email not verified';
            }
        } else {
            return 'Incorrect password';
        }
    } else {
        return 'User not found';
    }
}
function checkVerificationCode($enteredCode, $email, $pdo) {
  $query = "SELECT verification_code FROM Users WHERE email = :email";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array(':email' => $email));
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result && $result['verification_code'] == $enteredCode) {
    // Verification code is correct
    // Update the verified column in the database
    $updateQuery = "UPDATE Users SET verified = 1, verification_code = NULL WHERE email = :email";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute(array(':email' => $email));
    echo 1;
  } else {
    // Verification code is incorrect
    echo 0;
  }
}

function addGroup($groupName, $description, $ownerId)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Groups (group_name, description, owner_id) VALUES (?, ?, ?)");
    $stmt->execute([$groupName, $description, $ownerId]);
}

function addGroupMember($groupId, $userId)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Group_Members (group_id, user_id) VALUES (?, ?)");
    $stmt->execute([$groupId, $userId]);
}

function addPost($userId, $postText, $groupId = null)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Posts (user_id, post_text, group_id) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $postText, $groupId]);
}

function addComment($userId, $postId, $commentText)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Comments (user_id, post_id, comment_text) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $postId, $commentText]);
}

function addVote($userId, $postId, $voteType)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Votes (user_id, post_id, vote_type) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $postId, $voteType]);
}

function addChat($senderId, $receiverId, $message)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Chats (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$senderId, $receiverId, $message]);
}

function addPrivateChat($senderId, $receiverId, $message)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Private_Chats (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$senderId, $receiverId, $message]);
}

function addNotification($userId, $type, $message)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO Notifications (user_id, type, message) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $type, $message]);
}
// ========================
function getUniversities()
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Universities");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getUserById($userId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

function getUsersByUniversity($universityId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE university_id = ?");
    $stmt->execute([$universityId]);
    return $stmt->fetchAll();
}

function getGroupById($groupId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Groups WHERE group_id = ?");
    $stmt->execute([$groupId]);
    return $stmt->fetch();
}

function getGroupsByUser($userId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT g.* FROM Groups g INNER JOIN Group_Members m ON g.group_id = m.group_id WHERE m.user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getPostsByUser($userId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Posts WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getPostsByGroup($groupId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Posts WHERE group_id = ?");
    $stmt->execute([$groupId]);
    return $stmt->fetchAll();
}

function getPosts($university = null, $specialization = null)
{
    global $pdo;

    $query = "SELECT * FROM Posts";
    $params = array();

    if ($university) {
        $query .= " WHERE university = ?";
        $params[] = $university;
    }

    if ($specialization) {
        if (!$university) {
            $query .= " WHERE";
        } else {
            $query .= " AND";
        }

        $query .= " specialization = ?";
        $params[] = $specialization;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}

function getCommentsByPost($postId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Comments WHERE post_id = ?");
    $stmt->execute([$postId]);
    return $stmt->fetchAll();
}

function getVotesByPost($postId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Votes WHERE post_id = ?");
    $stmt->execute([$postId]);
    return $stmt->fetchAll();
}

function getChatsByUser($userId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Chats WHERE sender_id = ? OR receiver_id = ?");
    $stmt->execute([$userId, $userId]);
    return $stmt->fetchAll();
}


function getNotificationsByUser($userId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Notifications WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// =========================

function updateUser($userId, $firstName, $lastName, $email, $password)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE user_id = ?");
    return $stmt->execute([$firstName, $lastName, $email, $password, $userId]);
}

function updateGroup($groupId, $name, $description)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Groups SET name = ?, description = ? WHERE group_id = ?");
    return $stmt->execute([$name, $description, $groupId]);
}

function updatePost($postId, $content)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Posts SET content = ? WHERE post_id = ?");
    return $stmt->execute([$content, $postId]);
}

function updateComment($commentId, $content)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Comments SET content = ? WHERE comment_id = ?");
    return $stmt->execute([$content, $commentId]);
}

function updateVote($postId, $userId, $voteValue)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Votes SET vote_value = ? WHERE post_id = ? AND user_id = ?");
    return $stmt->execute([$voteValue, $postId, $userId]);
}

function updateChat($chatId, $content)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Chats SET content = ? WHERE chat_id = ?");
    return $stmt->execute([$content, $chatId]);
}

function updatePrivateChat($chatId, $content)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Private_Chats SET content = ? WHERE chat_id = ?");
    return $stmt->execute([$content, $chatId]);
}

function updateNotification($notificationId, $content)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Notifications SET content = ? WHERE notification_id = ?");
    return $stmt->execute([$content, $notificationId]);
}
// ============================================
function deleteUser($userId)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = ?");
    return $stmt->execute([$userId]);
}

function deleteGroup($groupId)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Groups WHERE group_id = ?");
    return $stmt->execute([$groupId]);
}

function deletePost($postId)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Posts WHERE post_id = ?");
    return $stmt->execute([$postId]);
}

function deleteComment($commentId)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Comments WHERE comment_id = ?");
    return $stmt->execute([$commentId]);
}

function deleteVote($postId, $userId)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Votes WHERE post_id = ? AND user_id = ?");
    return $stmt->execute([$postId, $userId]);
}

function deleteChat($chatId)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Chats WHERE chat_id = ?");
    return $stmt->execute([$chatId]);
}

function deletePrivateChat($chatId)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Private_Chats WHERE chat_id = ?");
    return $stmt->execute([$chatId]);
}

function deleteNotification($notificationId)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Notifications WHERE notification_id = ?");
    return $stmt->execute([$notificationId]);
}
