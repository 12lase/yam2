<?php
include 'config.php'; // Database configuration

$purchaseComplete = false;
$errorMessage = '';

try 
{
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Process form on POST request
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
        $selectedPart = $_POST['part'] ?? null;
        $selectedSupplier = $_POST['supplier'] ?? null;
        $quantityToBuy = $_POST['quantity'] ?? 0;

        if ($selectedPart && $selectedSupplier && $quantityToBuy > 0) 
        {
            // Begin transaction
            $pdo->beginTransaction();

            // Check current quantity
            $stmt = $pdo->prepare("SELECT QTY FROM SP WHERE S = :selectedSupplier AND P = :selectedPart");
            $stmt->bindParam(':selectedSupplier', $selectedSupplier);
            $stmt->bindParam(':selectedPart', $selectedPart);
            $stmt->execute();

            $currentQty = $stmt->fetchColumn();

            if ($currentQty !== false && $currentQty >= $quantityToBuy) 
            {
                // Update the quantity
                $newQty = $currentQty - $quantityToBuy;
                $updateStmt = $pdo->prepare("UPDATE SP SET QTY = :newQty WHERE S = :selectedSupplier AND P = :selectedPart");
                $updateStmt->bindParam(':newQty', $newQty);
                $updateStmt->bindParam(':selectedSupplier', $selectedSupplier);
                $updateStmt->bindParam(':selectedPart', $selectedPart);
                $updateStmt->execute();
                $pdo->commit();
                $purchaseComplete = true;
            } 
            
            else 
            {
                // Not enough quantity or part does not exist
                $errorMessage = "Not enough quantity available or part does not exist.";
                $pdo->rollBack();
            }
        } 
        
        else 
        {
            $errorMessage = "Please ensure you select a part, supplier, and quantity before submitting.";
        }
    }
} 

catch (PDOException $e) 
{
    $pdo->rollBack();
    $errorMessage = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buy Part</title>
</head>
<body>
<?php
// Show a message if purchase was completed or if there's an error
if ($purchaseComplete) 
{
    echo "<p>Purchase completed successfully!</p>";
} 

elseif ($errorMessage) 
{
    echo "<p>Error: $errorMessage</p>";
}
?>

<!-- Always show the form -->
<form action="buy_part.php" method="post">
    <label for="supplier">Select a Supplier:</label>
    <select name="supplier" id="supplier">
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
    <select name="part" id="part">
        <?php
        // Get all parts to populate the dropdown
        $stmt = $pdo->query("SELECT P, PNAME FROM P");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
        {
            echo "<option value=\"" . htmlspecialchars($row['P']) . "\">" . htmlspecialchars($row['PNAME']) . "</option>";
        }
        ?>
    </select>
    <label for="quantity">Quantity to buy:</label>
    <input type="number" name="quantity" id="quantity" min="1" value="1">
    <input type="submit" value="Buy Part">
</form>

<!-- Back to Menu button here -->
<a href="assign8.php" class="btn btn-default">Back to Menu</a>

</body>
</html>
