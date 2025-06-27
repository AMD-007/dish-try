<?php
include 'db.php';
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}
$id = $_GET['id'];
try {
    $result = $conn->query("SELECT * FROM recipes WHERE id = $id");
    if (!$result) {
        throw new Exception("Error fetching recipe details from the database.");
    }
    $recipe = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $comment = htmlspecialchars(trim($_POST['comment']));
        $rating = (int)$_POST['rating'];
        $email = $_SESSION['email'];

        if ($conn->query("INSERT INTO comments (recipe_id, email, comment, rating) VALUES ('$id', '$email', '$comment', '$rating')")) {
            header("Location: recipe.php?id=$id");
            exit();
        } else {
            throw new Exception("Failed to submit comment. Please try again.");
        }
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recipe['title']); ?> - Malaysian Dishes</title>
    <style>
        /* Global Styles */
        body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('xyz.png'); /* Replace with the actual path to your image */
    background-size: cover; /* Ensures the image covers the entire screen */
    background-position: center; /* Centers the image */
    background-repeat: no-repeat; /* Prevents tiling */
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    box-sizing: border-box;
}

        /* Container */
        .container {
            width: 70%;
            max-width: 1400px;
            background: #e8e8e8;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            color: #FF6F61;
            margin-bottom: 20px;
            font-size: 2.5em;
            position: relative;
        }

        h1 img {
            max-width: 80%;
            border-radius: 10px;
            margin-top: 10px;
        }

        h2 {
            color: #FF6F61;
            margin-top: 20px;
            font-size: 1.8em;
            border-bottom: 2px solid #FF6F61;
            padding-bottom: 10px;
            display: inline-block;
            margin-bottom: 15px;
        }

        p {
            line-height: 1.6;
            color: #555;
            margin-bottom: 20px;
            font-size: 1.2em;
        }

        /* Form Styles */
        form {
            margin-top: 20px;
            text-align: left;
            background: #F9F9F9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
        }

        form textarea, form select, form button {
            width: calc(100% - 20px);
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1em;
        }

        form button {
            background: #FF6F61;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            border: none;
        }

        form button:hover {
            background: #e45a4e;
            transform: translateY(-2px);
        }

        /* Comment Section */
        .comment {
            background: #f9f9f9;
            margin-top: 10px;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .comment strong {
            color: #333;
            font-weight: bold;
            font-size: 1.2em;
        }

        .comment p {
            margin: 5px 0;
            color: #555;
            font-size: 1.1em;
        }

        /* Error Message */
        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <?= htmlspecialchars($recipe['title']); ?>
            <img src="<?= htmlspecialchars($recipe['image']); ?>" alt="<?= htmlspecialchars($recipe['title']); ?> Image">
        </h1>
        <h2>Ingredients</h2>
        <p><?= nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>
        <h2>Cooking Instructions</h2>
        <p><?= nl2br(htmlspecialchars($recipe['instructions'])); ?></p>

        <?php if (isset($error_message)) : ?>
            <div class="error"><?= $error_message ?></div>
        <?php endif; ?>

        <section>
            <h2>Leave a Comment</h2>
            <form method="POST">
                <textarea name="comment" required placeholder="Leave a comment..."></textarea>
                <select name="rating" required aria-label="Rate this recipe">
                    <option value="" disabled selected>Rate this recipe</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
                <button type="submit">Submit</button>
            </form>
        </section>

        <section>
            <h2>Comments</h2>
            <?php
            $comments = $conn->query("SELECT * FROM comments WHERE recipe_id = $id ORDER BY id DESC");
            while ($comment = $comments->fetch_assoc()) {
            ?>
                <div class="comment">
                    <strong><?= htmlspecialchars($comment['email']); ?></strong>
                    <p><?= nl2br(htmlspecialchars($comment['comment'])); ?></p>
                    <p>Rating: <?= htmlspecialchars($comment['rating']); ?> Stars</p>
                </div>
            <?php } ?>
        </section>
    </div>
</body>
</html>
