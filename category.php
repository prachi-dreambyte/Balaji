<?php
// Include database connection
include 'connect.php';

// Fetch all categories with proper error handling
try {
    $sql = "SELECT * FROM categories ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Error fetching categories");
    }
    
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Categories - Balaji Furniture</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .category-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .category-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #f0f0f0;
        }
        
        .category-content {
            padding: 20px;
            text-align: center;
        }
        
        .category-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 10px 0;
        }
        
        .category-description {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background 0.3s ease;
        }
        
        .back-button:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <h1>🪑 Our Furniture Categories</h1>
            <p>Discover our wide range of quality furniture for every room</p>
        </div>
    </div>

    <div class="container">
        <!-- Error Message -->
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <i class="fa fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Categories Grid -->
        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <i class="fa fa-box-open"></i>
                <h3>No Categories Available</h3>
                <p>We're currently setting up our furniture categories. Please check back soon!</p>
                <a href="index.php" class="back-button">
                    <i class="fa fa-home"></i> Back to Home
                </a>
            </div>
        <?php else: ?>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                    <a href="shop.php?category_id=<?php echo intval($category['id']); ?>" class="category-card">
                        <img 
                            src="admin/uploads/<?php echo htmlspecialchars($category['category_image']); ?>" 
                            alt="<?php echo htmlspecialchars($category['category_name']); ?>"
                            class="category-image"
                            onerror="this.src='img/placeholder-category.jpg'"
                        >
                        <div class="category-content">
                            <h3 class="category-name"><?php echo htmlspecialchars($category['category_name']); ?></h3>
                            <p class="category-description">Explore our <?php echo htmlspecialchars($category['category_name']); ?> collection</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Back to Shop Button -->
        <div style="text-align: center; margin-top: 40px;">
            <a href="shop.php" class="back-button">
                <i class="fa fa-shopping-bag"></i> View All Products
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
