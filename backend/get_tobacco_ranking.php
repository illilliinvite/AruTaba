<?php

$conn = new mysqli(
    "localhost",
    "root",
    "arutaba",
    "arutaba"
);

$sql = "
    SELECT
        l.user_name,
        SUM(c.ciggarette_consumption) AS ciggarette_consumption
    FROM calender c
    JOIN login l
        ON c.user_id = l.user_id
    WHERE
        YEAR(c.osake_drinking) = YEAR(CURDATE())
        AND MONTH(c.osake_drinking) = MONTH(CURDATE())
    GROUP BY c.user_id, l.user_name
    ORDER BY ciggarette_consumption DESC
";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

header("Content-Type: application/json");
echo json_encode($data);

?>