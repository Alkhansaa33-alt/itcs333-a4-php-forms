<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/validate.php';

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$values = ['name' => '', 'email' => '', 'message' => ''];
$errors = [];
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    foreach ($values as $field => $default) {
        $submitted = $_POST[$field] ?? $default;
        $values[$field] = is_string($submitted) ? $submitted : $default;
    }

    $name = trim($values['name']);
    $email = trim($values['email']);
    $message = trim($values['message']);

    if (!validate_required($name)) {
        $errors['name'] = 'Name is required.';
    } elseif (!validate_length($name, 2, 60)) {
        $errors['name'] = 'Name must be 2 to 60 characters.';
    }

    if (!validate_email($email)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if (!validate_required($message)) {
        $errors['message'] = 'Message is required.';
    } elseif (!validate_length($message, 10, 1000)) {
        $errors['message'] = 'Message must be 10 to 1000 characters.';
    }

    if ($errors === []) {
        header('Location: index.php?success=1', true, 303);
        exit;
    }

    http_response_code(200);
}

$success = $method === 'GET' && ($_GET['success'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Contact Us</title>
</head>
<body>
  <h1>Contact Us</h1>

  <?php if ($success): ?>
    <p role="status">Your message was submitted successfully.</p>
    <p><a href="index.php">Back to the form</a></p>
  <?php else: ?>
    <form method="post" action="index.php" novalidate>
      <p>
        <label for="name">Name</label>
        <input type="text" id="name" name="name"
               value="<?= escape_html($values['name']) ?>">
        <?php if (isset($errors['name'])): ?>
          <span class="error"><?= escape_html($errors['name']) ?></span>
        <?php endif; ?>
      </p>

      <p>
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               value="<?= escape_html($values['email']) ?>">
        <?php if (isset($errors['email'])): ?>
          <span class="error"><?= escape_html($errors['email']) ?></span>
        <?php endif; ?>
      </p>

      <p>
        <label for="message">Message</label>
        <textarea id="message" name="message"><?= escape_html($values['message']) ?></textarea>
        <?php if (isset($errors['message'])): ?>
          <span class="error"><?= escape_html($errors['message']) ?></span>
        <?php endif; ?>
      </p>

      <button type="submit">Send</button>
    </form>
  <?php endif; ?>
</body>
</html>