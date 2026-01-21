<?php
$page = 'Manage Carousel';
$description = 'Manage homepage carousel banners';
$author = 'ASA Al-Mamun';
$title = 'Manage Carousel';

// Include database connection (though we mostly use JSON here)
include_once "../db/db.php";

$json_file = "../assets/js/carousel.json";
$message = "";
$messageType = "";

// Load carousel data
if (!file_exists($json_file)) {
    file_put_contents($json_file, json_encode([]));
}
$carousel_data = json_decode(file_get_contents($json_file), true);

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $new_data = [];
    foreach ($carousel_data as $item) {
        if ($item['id'] == $id) {
            // Delete image if it's local
            if (strpos($item['image'], 'uploads/banners/') !== false) {
                if (file_exists("../" . $item['image'])) {
                    unlink("../" . $item['image']);
                }
            }
            continue;
        }
        $new_data[] = $item;
    }
    $carousel_data = $new_data;
    if (file_put_contents($json_file, json_encode($carousel_data, JSON_PRETTY_PRINT))) {
        $message = "Banner deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error saving changes.";
        $messageType = "danger";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_banner'])) {
    $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : 0;
    $title_text = $_POST['title'];
    $description_text = $_POST['description'];
    $link = $_POST['link'];
    $image_url = $_POST['existing_image'];

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/banners/";
        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = time() . "_" . rand(1000, 9999) . "." . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image if editing
            if ($id > 0 && strpos($image_url, 'uploads/banners/') !== false) {
                if (file_exists("../" . $image_url)) {
                    unlink("../" . $image_url);
                }
            }
            $image_url = "uploads/banners/" . $new_filename;
        }
    }

    if ($id > 0) {
        // Edit existing
        foreach ($carousel_data as &$item) {
            if ($item['id'] == $id) {
                $item['title'] = $title_text;
                $item['description'] = $description_text;
                $item['link'] = $link;
                $item['image'] = $image_url;
                break;
            }
        }
    } else {
        // Add new
        $max_id = 0;
        foreach ($carousel_data as $item) {
            if ($item['id'] > $max_id) $max_id = $item['id'];
        }
        $carousel_data[] = [
            'id' => $max_id + 1,
            'title' => $title_text,
            'description' => $description_text,
            'link' => $link,
            'image' => $image_url
        ];
    }

    if (file_put_contents($json_file, json_encode($carousel_data, JSON_PRETTY_PRINT))) {
        $message = "Banner saved successfully!";
        $messageType = "success";
    } else {
        $message = "Error saving changes.";
        $messageType = "danger";
    }
}

// Get item for editing
$edit_item = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    foreach ($carousel_data as $item) {
        if ($item['id'] == $id) {
            $edit_item = $item;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>        
        <?php include "inc/head.php"; ?>
        <style>
            .banner-preview {
                max-width: 100px;
                max-height: 60px;
                object-fit: cover;
                border-radius: 4px;
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <?php include "inc/navbar.php"; ?>
        <div id="layoutSidenav">
            <?php include "inc/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Manage Carousel</h1>
                        <ol class="breadcrumb mb-4">                            
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Carousel</li>
                        </ol>

                        <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <!-- Form Section -->
                            <div class="col-xl-4">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-edit me-1"></i>
                                        <?php echo $edit_item ? 'Edit Banner' : 'Add New Banner'; ?>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?php echo $edit_item ? $edit_item['id'] : ''; ?>">
                                            <input type="hidden" name="existing_image" value="<?php echo $edit_item ? $edit_item['image'] : ''; ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Title</label>
                                                <input type="text" name="title" class="form-control" value="<?php echo $edit_item ? htmlspecialchars($edit_item['title']) : ''; ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3" required><?php echo $edit_item ? htmlspecialchars($edit_item['description']) : ''; ?></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Link</label>
                                                <input type="text" name="link" class="form-control" value="<?php echo $edit_item ? htmlspecialchars($edit_item['link']) : ''; ?>" placeholder="e.g. shop.php?category=electronics" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Banner Image</label>
                                                <input type="file" name="image" class="form-control" <?php echo $edit_item ? '' : 'required'; ?>>
                                                <?php if ($edit_item): ?>
                                                    <div class="mt-2 text-muted small">Current image: <?php echo $edit_item['image']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <button type="submit" name="save_banner" class="btn btn-primary w-100">
                                                <i class="fas fa-save me-1"></i> <?php echo $edit_item ? 'Update Banner' : 'Save Banner'; ?>
                                            </button>
                                            <?php if ($edit_item): ?>
                                                <a href="manage-carousel.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- List Section -->
                            <div class="col-xl-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-list me-1"></i>
                                        Banner List
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Details</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($carousel_data)): ?>
                                                        <?php foreach ($carousel_data as $item): ?>
                                                        <tr>
                                                            <td>
                                                                <?php 
                                                                    $img_path = $item['image'];
                                                                    if (strpos($img_path, 'http') === false) {
                                                                        $img_path = '../' . $img_path;
                                                                    }
                                                                ?>
                                                                <img src="<?php echo $img_path; ?>" class="banner-preview" alt="">
                                                            </td>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($item['title']); ?></strong><br>
                                                                <small class="text-muted"><?php echo htmlspecialchars(substr($item['description'], 0, 80)) . '...'; ?></small><br>
                                                                <small class="text-primary"><?php echo htmlspecialchars($item['link']); ?></small>
                                                            </td>
                                                            <td>
                                                                <a href="?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary mb-1">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger mb-1" onclick="return confirm('Delete this banner?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center">No banners found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <?php include "inc/footer.php"; ?>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
