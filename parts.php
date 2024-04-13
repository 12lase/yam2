<?php
include 'config.php'; // database configuration

try 
{
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to select all columns from the 'P' table
    $stmt = $pdo->query("SELECT P, PNAME, COLOR, WEIGHT FROM P"); 
    echo "<table border='1' style='width:100%; text-align:left;'><tr><th>P</th><th>Name</th><th>Color</th><th>Weight</th></tr>";
    
    // Fetch each row and output data in table format
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
    {
        echo "<tr><td>" . htmlspecialchars($row['P']) . "</td><td>" . 
        htmlspecialchars($row['PNAME']) . "</td><td>" . 
        htmlspecialchars($row['COLOR']) . "</td><td>" . 
        htmlspecialchars($row['WEIGHT']) . "</td></tr>";
    }
    echo "</table>";
} 

catch (PDOException $e) 
{
    echo "Error: " . $e->getMessage();
}
?>
