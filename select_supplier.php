<?php
include 'config.php'; // Database configuration

// Form processing
$selectedSupplier = isset($_POST['supplier']) ? $_POST['supplier'] : false;

try 
{
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Only run this part if a supplier was selected
    if ($selectedSupplier) 
    {
        // Query to get parts provided by the selected supplier
        $stmt = $pdo->prepare("SELECT SP.P, P.PNAME, SP.QTY FROM SP JOIN P ON SP.P = P.P WHERE SP.S = :selectedSupplier");
        $stmt->bindParam(':selectedSupplier', $selectedSupplier);
        $stmt->execute();
        
        echo "<table border='1' style='width:100%; text-align:left;'><tr><th>Part Number</th><th>Part Name</th><th>Quantity</th></tr>";
        // Fetch and display each row of data
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
        {
            echo "<tr><td>" . htmlspecialchars($row['P']) . "</td><td>" . 
            htmlspecialchars($row['PNAME']) . "</td><td>" . 
            htmlspecialchars($row['QTY']) . "</td></tr>";
        }
        echo "</table>";
    }
} 

catch (PDOException $e) 
{
    echo "Error: " . $e->getMessage();
}

// Always show the supplier selection form
?>
<form action="select_supplier.php" method="post">
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
    <input type="submit" value="Show Parts">
</form>
<!-- Back to Menu button here -->
<a href="assign8.php" class="btn btn-default">Back to Menu</a>
