<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (isset($_POST['search'])) {
    $customerID = trim($_POST['customerID']);
    $customerName = trim($_POST['customerName']);

    // Google Sheets CSV URL (replace with your sheet's URL)
    $googleSheetUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vSmmDh5fZgOgejtKRp0MAiv3QVAHnq9JGU2TmfZQdP9BCRvcHQLc2b9UrEw2O2Ucvlqpl95qNpoKqOr/pub?output=csv";

    // Fetch the data from Google Sheets
    $data = file_get_contents($googleSheetUrl);
    if ($data === false) {
        echo "<p>Error: Unable to fetch data from Google Sheets.</p>";
        exit();
    }

    // Parse the CSV data
    $rows = array_map('str_getcsv', explode("\n", $data));
    $headers = array_shift($rows); // Remove the first row (headers)

    $found = false;
    $result = "<table>
                <tr>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Project</th>
                    <th>Project Status</th>
                    <th>Payment Status</th>
                    <th>Static/Dynamic</th>
                    <th>Active/Inactive</th>
                    <th>Date</th>
                </tr>";

    foreach ($rows as $row) {
        if (count($row) < 8) continue; // Skip incomplete rows

        // Match customer ID and name
        if (strtolower(trim($row[0])) == strtolower($customerID) &&
            strtolower(trim($row[1])) == strtolower($customerName)) {
            $found = true;
            $result .= "<tr>
                        <td>{$row[0]}</td>
                        <td>{$row[1]}</td>
                        <td>{$row[2]}</td>
                        <td>{$row[3]}</td>
                        <td>{$row[4]}</td>
                        <td>{$row[5]}</td>
                        <td>{$row[6]}</td>
                        <td>{$row[7]}</td>
                      </tr>";
        }
    }

    if (!$found) {
        $result .= "<tr><td colspan='8'>No customer found with the given ID and Name.</td></tr>";
    }
    $result .= "</table>";

    echo $result;
}
?>
