<?php
include 'config.php'; // Database configuration

$assignmentComplete = false;
$errorMessage = '';

try 
{
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Process form on POST request
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
        $selectedSupplier = $_POST['supplier'] ?? null;
        $selectedPart = $_POST['part'] ?? null;
        $quantity = $_POST['quantity'] ?? 0;

        if ($selectedSupplier && $selectedPart && $quantity > 0) 
        {
            // Check if the part is already provided by the supplier
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM SP WHERE S = :selectedSupplier AND P = :selectedPart");
            $checkStmt->bindParam(':selectedSupplier', $selectedSupplier);
            $checkStmt->bindParam(':selectedPart', $selectedPart);
            $checkStmt->execute();

            if ($checkStmt->fetchColumn() > 0) 
            {
                // Update the quantity if the part is already provided by the supplier
                $updateStmt = $pdo->prepare("UPDATE SP SET QTY = QTY + :quantity WHERE S = :selectedSupplier AND P = :selectedPart");
            } 
            
            else 
            {
                // Insert new record if the part is not provided by the supplier
                $updateStmt = $pdo->prepare("INSERT INTO SP (S, P, QTY) VALUES (:selectedSupplier, :selectedPart, :quantity)");
            }

            $updateStmt->bindParam(':selectedSupplier', $selectedSupplier);
            $updateStmt->bindParam(':selectedPart', $selectedPart);
            $updateStmt->bindParam(':quantity', $quantity);
            $updateStmt->execute();
            
            $assignmentComplete = true;
        } 
        
        else 
        {
            $errorMessage = "Please ensure you select a supplier, part, and specify a valid quantity before submitting.";
        }
    }
} 

catch (PDOException $e) 
{
    $errorMessage = "Error: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Part to Supplier</title>
</head>
<body>
<?php
// Show a message if the assignment was completed or if there's an error
if ($assignmentComplete) 
{
    echo "<p>Part assigned to supplier successfully!</p>";
} 

elseif ($errorMessage) 
{
    echo "<p>$errorMessage</p>";
}
?>

<!-- Always show the form -->
<form action="assign_p_to_s.php" method="post">
    <label for="supplier">Select a Supplier:</label>
    <select name="supplier" id="supplier" required>
        <option value="">Choose a supplier</option>
        <?php
        // Get all suppliers to populate the dropdown
        $stmt = $pdo->query("SELECT S, SNAME FROM S");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
        {
            echo "<option value=\"" . htmlspecialchars($row['S']) . "\">" . htmlspecialchars($row['SNAME']) . "</option>";
        }
        ?>
    </select>
    <label for="part">Select a Part:</label>
    <select name="part" id="part" required>
        <option value="">Choose a part</option>
        <?php
        // Get all parts to populate the dropdown
        $stmt = $pdo->query("SELECT P, PNAME FROM P");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
        {
            echo "<option value=\"" . htmlspecialchars($row['P']) . "\">" . htmlspecialchars($row['PNAME']) . "</option>";
        }
        ?>
    </select>
    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" min="1" value="1" required>
    <input type="submit" value="Assign Part">
</form>

<!-- Back to Menu button here -->
<a href="assign8.php" class="btn btn-default">Back to Menu</a>

</body>
</html>
