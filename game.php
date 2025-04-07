<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$questions = [
    [
        "question" => "What is the capital city of Australia?", 
        "options" => ["Sydney", "Melbourne", "Canberra", "Perth"], 
        "correct" => "C", 
        "prize" => 100
    ],
    [
        "question" => "Which gas do plants absorb from the atmosphere?", 
        "options" => ["Oxygen", "Carbon Dioxide", "Nitrogen", "Hydrogen"], 
        "correct" => "B", 
        "prize" => 500
    ],
    [
        "question" => "How many continents are there on Earth?", 
        "options" => ["Seven", "Five", "Six", "Eight"], 
        "correct" => "A", 
        "prize" => 1000
    ],
    [
        "question" => "Which planet is known as the Red Planet?", 
        "options" => ["Earth", "Mars", "Saturn", "Venus"], 
        "correct" => "B", 
        "prize" => 2000
    ],
    [
        "question" => "Who painted the Mona Lisa?", 
        "options" => ["Vincent Van Gogh", "Leonardo da Vinci", "Pablo Picasso", "Claude Monet"], 
        "correct" => "B", 
        "prize" => 5000
    ],
    [
        "question" => "Which instrument has black and white keys?", 
        "options" => ["Guitar", "Drums", "Piano", "Flute"], 
        "correct" => "C", 
        "prize" => 10000
    ],
    [
        "question" => "What is the boiling point of water in Celsius?", 
        "options" => ["90°C", "100°C", "80°C", "120°C"], 
        "correct" => "B", 
        "prize" => 20000
    ],
    [
        "question" => "Which bird is the largest in the world?", 
        "options" => ["Ostrich", "Eagle", "Swan", "Peacock"], 
        "correct" => "A", 
        "prize" => 50000
    ],
    [
        "question" => "What is the chemical symbol for gold?", 
        "options" => ["Gd", "Au", "Ag", "Go"], 
        "correct" => "B", 
        "prize" => 75000
    ],
    [
        "question" => "Which scientist developed the theory of relativity?", 
        "options" => ["Isaac Newton", "Nikola Tesla", "Albert Einstein", "Marie Curie"], 
        "correct" => "C", 
        "prize" => 100000
    ]
];



// Initialize session variables for the game
if (!isset($_SESSION['questionIndex'])) {
    $_SESSION['questionIndex'] = 0;
    $_SESSION['new_prize'] = 0; 
    $_SESSION['secondChanceUsed'] = false; 
}

// If $_SESSION['new_prize'] is not set, initialize it to zero
if (!isset($_SESSION['new_prize'])) {
    $_SESSION['new_prize'] = 0;
}

// Set the current question
$currentQuestion = $questions[$_SESSION['questionIndex']]; 

$message = "";
$gameOver = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['option'])) {
        $selectedOption = $_POST['option']; 

        if ($selectedOption == $currentQuestion['correct']) {
            $_SESSION['new_prize'] += $currentQuestion['prize']; 
            $_SESSION['questionIndex']++; 
            
            if ($_SESSION['questionIndex'] >= count($questions)) {
                $message = "Congratulations! You've won $" . $_SESSION['new_prize'] . "!";
                $gameOver = true; 
                 
            } else {
                header("Location: game.php"); 
                exit();
            }
        } else {
            if (!$_SESSION['secondChanceUsed']) {
                $_SESSION['secondChanceUsed'] = true; 
                $message = "Incorrect answer. You have a second chance!";
            } else {
                $message = "Game Over! You won $" . $_SESSION['new_prize'] . "!";
                $gameOver = true; 
                 
            }
        }
    }

    if (isset($_POST['exitGame'])) {
        $message = "Game Over! You won $" . number_format($_SESSION['new_prize']) . "!";
        $gameOver = true; 
         
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Who Wants to Be a Millionaire</title>
    <link rel="stylesheet" href="styles2.css">
    <style>
        #feedbackForm {
            display: <?php echo $gameOver ? 'block' : 'none'; ?>; 
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <h1>WHO WANTS TO BE A</h1>
            <h2>MILLIONAIRE</h2>
        </div>
        <form method="POST" action="">
            <button type="submit" name="exitGame" class="exit-button">Exit Game</button> 
        </form>
    </header>
    <div class="game-container">
        
        <div class="prize-ladder">
            <ul>
                <?php
                $prizes = [
                    100,
                    500,
                    1000,
                    2000,
                    5000,
                    10000,
                    20000,
                    50000,
                    75000,
                    100000
                ];
                
                for ($i = 0; $i < count($prizes); $i++) {
                    $activeClass = ($i === $_SESSION['questionIndex']) ? 'active' : '';
                    echo "<li class='prize $activeClass'>" . ($i + 1) . " <span>\$" . number_format($prizes[$i]) . "</span></li>";
                }
                ?>
            </ul>
        </div>

        <div class="question-container">
            <div class="question">
                <?php echo $currentQuestion["question"]; ?>
            </div>
            
            <form method="POST" action="">
                <div class="options">
                    <?php
                    $option_labels = ["A", "B", "C", "D"];
                    foreach ($currentQuestion["options"] as $index => $option) {
                        echo "<button type='submit' name='option' value='{$option_labels[$index]}' class='option'>{$option_labels[$index]}: {$option}</button>";
                    }
                    ?>
                </div>
            </form>

            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="total-prize">
                <h3>Total Prize Money: $<?php echo number_format($_SESSION['new_prize']); ?></h3>
            </div>

            <!-- Feedback Form -->
            <div id="feedbackForm">
                <h2>Feedback</h2>
                <form method="POST" action="submit_feedback.php">
                    <div class="star-rating">
                        <input type="radio" id="5-stars" name="rating" value="5" required><label for="5-stars">&#9733;</label>
                        <input type="radio" id="4-stars" name="rating" value="4" required><label for="4-stars">&#9733;</label>
                        <input type="radio" id="3-stars" name="rating" value="3" required><label for="3-stars">&#9733;</label>
                        <input type="radio" id="2-stars" name="rating" value="2" required><label for="2-stars">&#9733;</label>
                        <input type="radio" id="1-star" name="rating" value="1" required><label for="1-star">&#9733;</label>
                    </div>
                    <br>
                    <label for="comments">Your Comments:</label><br>
                    <textarea id="comments" name="comments" rows="4" cols="50" required></textarea><br><br>
                    <input type="submit" value="Submit Feedback">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
