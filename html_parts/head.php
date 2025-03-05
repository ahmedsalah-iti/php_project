<!-- html_parts/head.php -->
<!DOCTYPE html>
<html lang="ar-en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Cafeteria'; ?> - Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="./assets/css/styles.css" rel="stylesheet">
    <?php if (isset($_GET['action']) && $_GET['action'] === 'admin'): ?>
        <link href="./assets/css/admin_styles.css" rel="stylesheet">
    <?php endif; ?>
</head>