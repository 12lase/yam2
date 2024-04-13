<?php
include 'config.php'; // Database configuration

$partAdded = false;
$newId = ''; // Variable to store the newly generated ID

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $partName = $_POST['pname'] ?? '';
    $partColor = $_POST['color'] ?? '';
    $partWeight = $_POST['weight'] ?? 0;

    try 
    {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start a transaction
        $pdo->beginTransaction();

        // Get the last part ID
        $stmt = $pdo->query("SELECT P FROM P ORDER BY P DESC LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastId = $row['P'] ?? 'P0'; // If there's no part, start with 'P0'
        
        // Increment the ID
        $number = intval(substr($lastId, 1)) + 1; // Extract the number and increment
        $newId = 'P' . str_pad($number, strlen($lastId) - 1, '0', STR_PAD_LEFT);

        // Prepare SQL statement to insert the new part with the new ID
        $stmt = $pdo->prepare("INSERT INTO P (P, PNAME, COLOR, WEIGHT) VALUES (:newId, :partName, :partColor, :partWeight)");
        $stmt->bindParam(':newId', $newId);
        $stmt->bindParam(':partName', $partName);
        $stmt->bindParam(':partColor', $partColor);
        $stmt->bindParam(':partWeight', $partWeight);

        // Execute the statement
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        $partAdded = true;
    } 
    
    catch (PDOException $e) 
    {
        $pdo->rollBack(); // Rollback the transaction on error
        echo "Error: " . $e->getMessage();
    }
}

// Show a message if the part was added
if ($partAdded) 
{
    echo "New part added successfully with ID {$newId}!";
}

// The form remains unchanged
?>
<form action="add_part.php" method="post">
    <label for="pname">Part Name:</label>
    <input type="text" name="pname" id="pname" required>

    <label for="color">Color:</label>
    <input type="text" name="color" id="color" required>

    <label for="weight">Weight:</label>
    <input type="number" name="weight" id="weight" min="1" required>

    <input type="submit" value="Add Part">
</form>

<!-- Back to Menu button here -->
<a href="assign8.php" class="btn btn-default">Back to Menu</a>
