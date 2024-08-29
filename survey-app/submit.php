<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $satisfaction = $_POST['satisfaction'];
    $feedback = $_POST['feedback'];
    
    $file = fopen("responses.csv", "a");
    fputcsv($file, [$name, $age, $satisfaction, $feedback]);
    fclose($file);
    
    echo "アンケートのご協力ありがとうございました！";
}
?>