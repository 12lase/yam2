<?php
include 'config.php'; // Database configuration

// Form processing
$selectedPart = isset($_POST['part']) ? $_POST['part'] : false;

try 
{
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Only run this part if a part was selected
    if ($selectedPart) 
    {
        // Query to get suppliers for the selected part
        $stmt = $pdo->prepare("SELECT SP.S, S.SNAME, SP.QTY FROM SP JOIN S ON SP.S = S.S WHERE SP.P = :selectedPart");
        $stmt->bindParam(':selectedPart', $selectedPart);
        $stmt->execute();
        
        echo "<table border='1' style='width:100%; text-align:left;'><tr><th>S</th><th>S</th><th>Quantity</th></tr>";
        // Fetch and display each row of data
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
        {
            echo "<tr><td>" . htmlspecialchars($row['S']) . "</td><td>" . 
            htmlspecialchars($row['SNAME']) . "</td><td>" . 
            htmlspecialchars($row['QTY']) . "</td></tr>";
        }
        echo "</table>";
    }
}

catch (PDOException $e) 
{
    echo "Error: " . $e->getMessage();
}

// Always show the parts selection form
?>
<form action="select_part.php" method="post">
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
    <input type="submit" value="Show Suppliers">
</form>

<!-- Back to Menu button here -->
<a href="assign8.php" class="btn btn-default">Back to Menu</a>
