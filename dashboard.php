<?php
include 'db.php';
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

// Default query to get all recipes
$query = "SELECT * FROM recipes";

// Check if a search term or ingredients are provided
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = $conn->real_escape_string(trim($_GET['search']));
    $query = "SELECT * FROM recipes WHERE title LIKE '%$searchTerm%'";
} elseif (isset($_GET['ingredients']) && !empty(trim($_GET['ingredients']))) {
    $ingredients = $conn->real_escape_string(trim($_GET['ingredients']));
    $query = "SELECT * FROM recipes WHERE ingredients LIKE '%$ingredients%'";
}

try {
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Error fetching recipes from the database.");
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
    <title>Recipes - Malaysian Dishes</title>
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
            width: 100%;
            max-width: 1310px;
            background: #e8e8e8;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            box-sizing: border-box;
        }

        /* Header */
        h1 {
            text-align: center;
            color: #FF6F61;
            margin-bottom: 20px;
            font-size: 2.8em;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        p {
            text-align: center;
            font-size: 1.1em;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        /* Search Form */
        form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    margin-bottom: 30px;
}

form input, form button {
    padding: 12px 15px; /* Ensures both inputs and button have equal padding */
    font-size: 1.1em; /* Matches the font size */
    border: 1px solid #ccc; /* Consistent border */
    border-radius: 8px; /* Rounded corners */
    box-sizing: border-box; /* Includes padding and border in size calculation */
}

form input {
    flex: 1; /* Ensures input stretches proportionally */
    min-width: 200px; /* Prevents input from shrinking too much */
}

form button {
    background: #FF6F61;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}

form button:hover {
    background: #e65c53;
    transform: translateY(-2px);
}

form button:active {
    transform: translateY(0);
}


        /* Recipes */
        .recipes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .recipe {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center; /* Center elements horizontally */
    text-align: center;  /* Ensure text aligns properly */
}

        .recipe:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .recipe img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .recipe h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.5em;
        }

        .recipe p {
            color: #666;
            margin-bottom: 15px;
            font-size: 1em;
            line-height: 1.5;
        }

        .recipe a {
    text-decoration: none;
    color: #FF6F61;
    border: 2px solid #FF6F61;
    padding: 10px 15px;
    border-radius: 5px;
    transition: background 0.3s, color 0.3s;
    margin-top: 10px;
    display: inline-block; /* Ensures the button is centered */
}

        .recipe a:hover {
            background: #FF6F61;
            color: #fff;
        }
        .upload-button {
    display: inline-block;
    padding: 12px 15px;
    font-size: 1.1em;
    background: #FF6F61;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.3s, transform 0.2s;
    text-align: center;
}

.upload-button:hover {
    background: #e65c53;
    transform: translateY(-2px);
}

.upload-button:active {
    transform: translateY(0);
}


        /* Loading Spinner */
        .spinner-border {
            display: none;
            width: 2rem;
            height: 2rem;
            border: 0.25em solid #f3f3f3;
            border-radius: 50%;
            border-top: 0.25em solid #FF6F61;
            animation: spin 1s linear infinite;
        }

        /* Spinner Animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Error Message */
        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 1em;
            color: #777;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 2.5em;
            }

            .recipe {
                padding: 15px;
            }

            .recipe img {
                max-width: 100%;
            }

            form input {
                width: calc(100% - 24px);
            }
        }

        @media (max-width: 500px) {
            h1 {
                font-size: 1.8em;
            }

            p {
                font-size: 1em;
            }

            .recipe {
                padding: 10px;
                text-align: center;
            }

            .recipe h2 {
                font-size: 1.3em;
            }

            .recipe p {
                font-size: 0.9em;
            }

            .recipe a {
                width: 100%;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Malaysian Dishes</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['email']); ?></p>

        <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap;">
    <input type="text" name="search" placeholder="Search by recipe title..." value="<?= htmlspecialchars($_GET['search'] ?? ''); ?>">
    <input type="text" name="ingredients" placeholder="Search by ingredients..." value="<?= htmlspecialchars($_GET['ingredients'] ?? ''); ?>">
    <button type="submit">Search</button>
    <a href="upload_recipe.php" class="upload-button">Upload New Recipe</a>
</form>

        <?php if (isset($error_message)) : ?>
            <div class="error"><?= $error_message ?></div>
        <?php else : ?>
            <div class="recipes">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <div class="recipe">
                        <img src="<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['title']); ?> Image">
                        <h2><?= htmlspecialchars($row['title']); ?></h2>
                        <p><?= htmlspecialchars($row['description']); ?></p>
                        <a href="recipe.php?id=<?= $row['id']; ?>">View Recipe</a>
                    </div>
                <?php } ?>
            </div>
        <?php endif; ?>

        <div class="footer">
            <p>Explore the vibrant flavors of Malaysia!</p>
        </div>
    </div>
</body>
</html>