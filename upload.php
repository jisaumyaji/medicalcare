<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if (isset($_FILES["prescription"]) && $_FILES["prescription"]["error"] == 0) {
        $targetDir = "uploads/"; // Directory where files will be saved
        $targetFile = $targetDir . basename($_FILES["prescription"]["name"]); // Path to save the file
        
        // Check file type
        $fileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
        if($fileType != "pdf" && $fileType != "jpg" && $fileType != "jpeg" && $fileType != "png") {
            echo "Sorry, only PDF, JPG, JPEG, PNG files are allowed.";
            exit();
        }
        
        // Check file size (max 5MB)
        if ($_FILES["prescription"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            exit();
        }
        
        // If file does not already exist, move it to the specified directory
        if (!file_exists($targetFile)) {
            if (move_uploaded_file($_FILES["prescription"]["tmp_name"], $targetFile)) {
                // File uploaded successfully, now you can store the file path in your database
                $filePath = $targetFile; // Change this according to your database structure

                // Connect to your database (replace placeholders with your actual database credentials)
                $servername = "localhost";
                $username = "root";
                $password = "root";
                $dbname = "medical_care_db";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Insert file path into database
                $sql = "INSERT INTO prescriptions (file_path) VALUES ('$filePath')";

                if ($conn->query($sql) === TRUE) {
                    echo "Prescription uploaded successfully.";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

                $conn->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Sorry, file already exists.";
        }
    } else {
        echo "Error: No file uploaded.";
    }
}
?>
