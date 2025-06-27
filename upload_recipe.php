<?php
include 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $ingredients = $conn->real_escape_string(trim($_POST['ingredients']));
    $instructions = $conn->real_escape_string(trim($_POST['instructions']));
    $image = $_FILES['image'];

    // Validation
    if (empty($title) || empty($description) || empty($ingredients) || empty($instructions) || empty($image['name'])) {
        $error_message = "All fields are required.";
    } else {
        // Handle image upload
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create directory if it doesn't exist
        }
        $uniqueFileName = uniqid("recipe_", true);
        $targetFile = $targetDir . $uniqueFileName;
        $imageFileType = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION)); // Initialize $imageFileType

        // Validate image
        $check = getimagesize($image['tmp_name']);
        if ($check === false) {
            $error_message = "File is not an image.";
        } elseif ($image['size'] > 500000) { // 500KB limit
            $error_message = "Sorry, your file is too large.";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error_message = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                // Insert recipe into database
                $query = "INSERT INTO recipes (title, description, ingredients, instructions, image) VALUES ('$title', '$description', '$ingredients', '$instructions', '$targetFile')";

                if ($conn->query($query) === TRUE) {
                    $success_message = "New recipe uploaded successfully!";
                } else {
                    $error_message = "Database Error: " . $conn->error;
                }
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Recipe - Malaysian Dishes</title>
    <style>
        /* Internal Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: url('xyz.png') no-repeat center center fixed; /* Background image with center alignment and fixed position */
            background-size: cover; /* Cover the entire viewport */
            color: black; /* White text for better contrast with the background */
        }
        .container {
            max-width: 850px;
            margin: auto;
            padding: 30px;
            border: 1px solid black;
            border-radius: 15px;
            background:rgba(249, 249, 249, 0.94); /* Slightly transparent background to keep text readable */
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            text-align: center;
            color: #FF6F61;
            margin-bottom: 20px;
            font-size: 2.8em;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }
        .error {
            color: #dc3545; /* Red color for error messages */
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
        .success {
            color: #28a745; /* Green color for success messages */
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
        textarea, input[type="text"], input[type="file"], button {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid black; /* Light gray border for input fields */
            border-radius: 8px;
            font-size: 16px;
        }
        textarea {
            height: 120px;
            resize: vertical;
        }
        input[type="file"] {
            padding: 0;
        }
        button {
            background-color: #FF6F61; /* Blue background for the button */
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transitions */
            padding: 15px;
            border-radius: 8px;
            font-size: 18px;
        }
        button:hover {
            background-color: #e65c53; /* Darker blue on hover */
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.2); /* Drop shadow on hover for better focus */
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 16px;
            color: #6c757d; /* Darker gray for footer text */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload New Recipe</h1>

        <?php if (isset($error_message)) : ?>
            <div class="error"><?= $error_message ?></div>
        <?php elseif (isset($success_message)) : ?>
            <div class="success"><?= $success_message ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="title">Recipe Title:</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea>

            <label for="ingredients">Ingredients:</label>
            <textarea name="ingredients" id="ingredients" required></textarea>

            <label for="instructions">Instructions:</label>
            <textarea name="instructions" id="instructions" required></textarea>

            <label for="image">Image:</label>
            <input type="file" name="image" id="image" accept="image/*" required>

            <button type="submit">Upload Recipe</button>
        </form>

        <div class="footer">
            <p>Explore the vibrant flavors of Malaysia!</p>
        </div>
    </div>
</body>
</html>
