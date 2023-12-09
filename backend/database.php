
<?php
include_once "user.php";
include_once "Item.php";
function userExists($guid): bool
{
    $conn = new mysqli("localhost", "myuser", "mypassword", "mydatabase", "3306");
    if ($conn->connect_error) {
        //TODO: notify user?
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT guid FROM users WHERE guid = ?");
    $stmt->bind_param("s", $guid);

    $stmt->execute();

    $stmt->store_result();

    $userExists = $stmt->num_rows > 0;

    $stmt->close();
    $conn->close();

    return $userExists;
}

function queryUser($guid): User
{
    $conn = new mysqli("localhost", "myuser", "mypassword", "mydatabase", "3306");
    if ($conn->connect_error) {
        //TODO: notify user?
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT user_id, guid, password FROM users WHERE guid = ?");
    $stmt->bind_param("s", $guid);

    $stmt->execute();

    $user = User::emptyUser();
    $stmt->bind_result($user->id, $user->guid, $user->password);

    if ($stmt->fetch()) {
        $stmt->close();
        $conn->close();
        return $user;
    } else {
        $stmt->close();
        $conn->close();
        return User::emptyUser();
    }
}

function registerUser(User $user): int
{
    $conn = new mysqli("localhost", "myuser", "mypassword", "mydatabase", "3306");
    if ($conn->connect_error) {
        echo "FUCKBASE";
        die("Connection failed: " . $conn->connect_error);
    }

    $password_hash = password_hash($user->password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (guid, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $user->guid, $password_hash);

    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $userId = $stmt->insert_id;

    $stmt->close();
    $conn->close();

    return $userId;
}

function updateUser($currentGuid, User $user): void
{
    $conn = new mysqli("localhost", "myuser", "mypassword", "mydatabase", "3306");
    if ($conn->connect_error) {
        echo "FUCKBASE";
        die("Connection failed: " . $conn->connect_error);
    }

    $passwordHash = password_hash($user->password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET guid = ?, password = ? WHERE guid = ?");
    $stmt->bind_param("sss", $user->guid, $passwordHash, $currentGuid);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}

function categoryExists($category): bool
{
    $conn = new mysqli("localhost", "myuser", "mypassword", "mydatabase", "3306");
    if ($conn->connect_error) {
        echo "FUCKBASE";
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT category_id FROM categories WHERE name = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();

    $stmt->store_result();

    $categoryExists = $stmt->num_rows > 0;

    $stmt->close();
    $conn->close();

    return $categoryExists;
}

function insertItem(Item $item): int
{
    $conn = new mysqli("localhost", "myuser", "mypassword", "mydatabase", "3306");
    if ($conn->connect_error) {
        error_log("Failed cock");
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO items (name, price, seller_id) VALUES (?,?,?)");
    $stmt->bind_param("sdi", $item->name, $item->price, $item->seller_id);
    $stmt->execute();
    $itemId = $stmt->insert_id;
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO item_categories (item_id, category_name) VALUES (?, ?)");
    foreach ($item->categories as $categoryName) {
        $stmt->bind_param("is", $itemId, $categoryName);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    return $itemId;
}

function fetchItem($buyerId): ?Item
{
    $conn = new mysqli("localhost", "myuser", "mypassword", "mydatabase", "3306");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("
    SELECT items.* FROM items
    LEFT JOIN user_bought_items ON items.item_id = user_bought_items.item_id
    WHERE items.seller_id != ? AND user_bought_items.item_id IS NULL
    ORDER BY RAND()
    LIMIT 1
");


    $stmt->bind_param("i", $buyerId);
    if ($stmt->execute() === false) {
        die("Failed to execute statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $item = Item::emptyItem();

    if ($row === null) {
        return null;
    }

    $item->itemId = $row["item_id"];
    $item->name = $row["name"];
    $item->price = $row["price"];
    $item->seller_id = $row["seller_id"];

    $stmt = $conn->prepare("SELECT category_name FROM item_categories WHERE item_id = ?");
    $stmt->bind_param("i", $item->itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        array_push($item->categories, $row["category_name"]);
    }

    $stmt->close();
    $conn->close();

    return $item;
}

function linkItemToUser($user_id, $itemId): void
{
    $conn = new mysqli("localhost", "myuser", "mypassword", "mydatabase", "3306");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO user_bought_items (user_id, item_id, date_of_purchase) VALUES (?, ?, NOW())");

    $stmt->bind_param("ii", $user_id, $itemId);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}
