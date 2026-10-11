<?php

$dsn = 'mysql:host=localhost;port=3306;dbname=myapp;charset=utf8mb4';
$pdo = new PDO($dsn, 'root', '');

$errors = [];
$welcomeMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO 1: Validate name and city
    $name = trim($_POST['name'] ?? '');
    $age  = trim($_POST['age'] ?? '');
    $city = trim($_POST['city'] ?? '');

    if ($name === '') {
        $errors['name'] = 'Name is required';
    }

    if ($city === '') {
        $errors['city'] = 'City is required';
    }

    // TODO 2: If no errors, INSERT with prepared statement
    if (empty($errors)) {
        $statement = $pdo->prepare(
            'INSERT INTO users (name, age, city) VALUES (:name, :age, :city)'
        );

        $statement->execute([
            'name' => $name,
            'age'  => $age !== '' ? (int) $age : null,
            'city' => $city
        ]);

        // Escape for the welcome message
        $safeName = htmlspecialchars($name);
        $safeCity = htmlspecialchars($city);
        $welcomeMessage = "Welcome {$safeName} from {$safeCity}!";
    }
}

// Bonus: Get all users
$users = $pdo->query('SELECT * FROM users ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
</head>
<body>
    <h1>Sign Up</h1>

    <!-- TODO 3: Show errors -->
    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        <p>
            <input name="name" placeholder="Name" 
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </p>
        <p>
            <input name="age" placeholder="Age" 
                   value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
        </p>
        <p>
            <input name="city" placeholder="City" 
                   value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
        </p>
        <button>Sign Up</button>
    </form>

    <!-- TODO 4: Welcome message -->
    <?php if ($welcomeMessage): ?>
        <p style="color: green; font-weight: bold;">
            <?= $welcomeMessage ?>
        </p>
    <?php endif; ?>

    <!-- Bonus: List all users -->
    <hr>
    <h2>All Users</h2>

    <?php if (count($users) > 0): ?>
        <ul>
            <?php foreach ($users as $user): ?>
                <li>
                    <?= htmlspecialchars($user['name']) ?>
                    (Age: <?= htmlspecialchars($user['age'] ?? 'N/A') ?>)
                    from <?= htmlspecialchars($user['city']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No users yet.</p>
    <?php endif; ?>

</body>
</html>