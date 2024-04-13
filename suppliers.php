<?php
include 'config.php'; // database configuration

try 
{
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to select all columns from the 'S' table
    $stmt = $pdo->query("SELECT S, SNAME, STATUS, CITY FROM S"); 
    echo "<table border='1' style='width:100%; text-align:left;'><tr><th>S</th><th>Name</th><th>Status</th><th>City</th></tr>";
    
    // Fetch each row and output data in table format
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
    {
        echo "<tr><td>" . htmlspecialchars($row['S']) . "</td><td>" . 
        htmlspecialchars($row['SNAME']) . "</td><td>" . 
        htmlspecialchars($row['STATUS']) . "</td><td>" . 
        htmlspecialchars($row['CITY']) . "</td></tr>";
    }
    echo "</table>";
} 

catch (PDOException $e) 
{
    echo "Error: " . $e->getMessage();
}
?>

