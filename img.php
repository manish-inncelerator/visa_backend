<?php
// index.php

// Get id and country from URL parameters
$id = isset($_GET['id']) ? $_GET['id'] : '';
$country = isset($_GET['country']) ? $_GET['country'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Search </title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --bg-color: #f8f9fa;
            --text-color: #333;
            --card-bg: #fff;
            --primary-color: #007bff;
            --hover-color: #0056b3;
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #f8f9fa;
            --card-bg: #2d2d2d;
            --primary-color: #0d6efd;
            --hover-color: #0b5ed7;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .search-container h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .search-form .input-group {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .search-form .form-control {
            border: none;
            padding: 15px;
            font-size: 1rem;
        }

        .search-form .btn {
            background-color: var(--primary-color);
            border: none;
            padding: 15px 30px;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .search-form .btn:hover {
            background-color: var(--hover-color);
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .image-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .image-card img:hover {
            transform: scale(1.05);
        }

        .image-card .card-body {
            padding: 15px;
            text-align: center;
        }

        .image-card .btn {
            background-color: #28a745;
            border: none;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .image-card .btn:hover {
            background-color: #218838;
        }

        .load-more {
            text-align: center;
            margin: 40px 0;
        }

        .load-more .btn {
            background-color: var(--primary-color);
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .load-more .btn:hover {
            background-color: var(--hover-color);
        }

        .load-more .btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: var(--card-bg);
            color: var(--text-color);
            margin-top: 40px;
        }

        .footer a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .dark-mode-toggle {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dark-mode-toggle:hover {
            background-color: var(--hover-color);
        }

        .skeleton {
            background-color: #e0e0e0;
            border-radius: 12px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.6;
            }
        }
    </style>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="search-container">
        <h1 class="text-center mb-4">Image Search <span class="badge bg-success">BETA</span></h1>
        <form method="GET" class="search-form mb-4">
            <div class="input-group">
                <input type="text" name="query" class="form-control" placeholder="Search for images..." required>
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <div class="image-grid" id="imageGrid">
            <!-- Skeleton Loading -->
            <div class="image-card skeleton" style="height: 300px;"></div>
            <div class="image-card skeleton" style="height: 300px;"></div>
            <div class="image-card skeleton" style="height: 300px;"></div>
        </div>
        <div class="load-more">
            <button id="loadMore" class="btn btn-primary">Load More</button>
        </div>
    </div>

    <div class="footer">
        <p><?= date('Y'); ?> &copy; Image Search</p>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            <span id="toastMessage"></span>
        </div>
    </div>

    <!-- Dark Mode Toggle -->
    <button class="dark-mode-toggle" id="darkModeToggle">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        let currentPage = 1;
        const query = "<?php echo isset($_GET['query']) ? $_GET['query'] : ''; ?>";
        const id = "<?php echo $id; ?>";
        const country = "<?php echo $country; ?>";
        const imageGrid = document.getElementById('imageGrid');
        const loadMoreButton = document.getElementById('loadMore');

        // Load more images when the "Load More" button is clicked
        loadMoreButton.addEventListener('click', () => {
            currentPage++;
            loadMoreImages(currentPage);
        });

        // Function to load more images
        function loadMoreImages(page) {
            // Disable the "Load More" button and show loading text
            loadMoreButton.disabled = true;
            loadMoreButton.textContent = 'Loading...';

            // Add skeleton loading placeholders
            addSkeletonLoading();

            // Fetch images from the API
            fetch(`http://localhost/visa/admin/imageSearch.php?query=${query}&per_page=10&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    // Remove skeleton loading placeholders
                    removeSkeletonLoading();

                    // Display fetched images
                    if (data.pexels && data.pexels.length > 0) {
                        data.pexels.forEach(photo => {
                            const fileName = 'pexels_' + photo.src.original.split('/').pop();
                            createImageCard(photo.src.medium, photo.src.large, photo.photographer, photo.src.original, fileName);
                        });
                    }

                    if (data.pixabay && data.pixabay.length > 0) {
                        data.pixabay.forEach(photo => {
                            const fileName = 'pixabay_' + photo.largeImageURL.split('/').pop();
                            createImageCard(photo.webformatURL, photo.largeImageURL, photo.tags, photo.largeImageURL, fileName);
                        });
                    }

                    // Update the "Load More" button state
                    if ((!data.pexels || data.pexels.length === 0) && (!data.pixabay || data.pixabay.length === 0)) {
                        loadMoreButton.disabled = true;
                        loadMoreButton.textContent = 'No more images';
                    } else {
                        loadMoreButton.disabled = false;
                        loadMoreButton.textContent = 'Load More';
                    }
                })
                .catch(error => {
                    console.error('Error loading more images:', error);
                    removeSkeletonLoading(); // Remove skeleton loading on error
                    loadMoreButton.disabled = false;
                    loadMoreButton.textContent = 'Load More';
                    showToast('Failed to load images. Please try again.');
                });
        }

        // Function to create an image card
        function createImageCard(imageUrl, largeImageUrl, altText, downloadUrl, fileName) {
            const card = `
            <div class="image-card">
                <img src="${imageUrl}" alt="${altText}" onclick="openImageModal('${largeImageUrl}')">
                <div class="card-body">
                    <button class="btn btn-success" onclick="saveImage('${downloadUrl}', '${fileName}')">
                        Save Image
                    </button>
                </div>
            </div>
        `;
            imageGrid.insertAdjacentHTML('beforeend', card);
        }

        // Function to add skeleton loading placeholders
        function addSkeletonLoading() {
            const skeletonCount = 6; // Number of skeleton placeholders to show
            for (let i = 0; i < skeletonCount; i++) {
                const skeleton = document.createElement('div');
                skeleton.className = 'image-card skeleton';
                skeleton.style.height = '300px';
                imageGrid.appendChild(skeleton);
            }
        }

        // Function to remove skeleton loading placeholders
        function removeSkeletonLoading() {
            const skeletons = imageGrid.querySelectorAll('.skeleton');
            skeletons.forEach(skeleton => skeleton.remove());
        }

        // Function to save image metadata and trigger PHP to download the image
        function saveImage(imageUrl, fileName) {
            const sanitizedFileName = fileName.split('?')[0]; // Remove query parameters from the filename

            // Send metadata to the PHP backend
            const payload = {
                id: id,
                country: country,
                filename: sanitizedFileName,
                imageUrl: imageUrl
            };

            fetch('api/v1/savePhoto.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.redirect) {
                        // Redirect to the provided URL
                        window.location.href = data.redirect;
                    } else {
                        // Show the message from the server response
                        showToast(data.message || 'Image saved successfully.');
                    }
                })
                .catch(error => {
                    console.error('Error saving image:', error);
                    showToast('Failed to save image.');
                });
        }


        // Function to show a toast notification
        function showToast(message) {
            const toastMessage = document.getElementById('toastMessage');
            toastMessage.textContent = message;
            const toast = new bootstrap.Toast(document.getElementById('toast'));
            toast.show();
        }

        // Function to open an image in a modal
        function openImageModal(imageUrl) {
            const modal = `
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="${imageUrl}" class="img-fluid" alt="Preview">
                        </div>
                    </div>
                </div>
            </div>
        `;

            // Append the modal to the body and show it
            document.body.insertAdjacentHTML('beforeend', modal);
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();

            // Remove the modal from the DOM after it's closed
            document.getElementById('imageModal').addEventListener('hidden.bs.modal', () => {
                document.getElementById('imageModal').remove();
            });
        }

        // Initial load of images if query is set
        if (query) {
            loadMoreImages(currentPage);
        }
        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        darkModeToggle.addEventListener('click', () => {
            document.body.dataset.theme = document.body.dataset.theme === 'dark' ? 'light' : 'dark';
            darkModeToggle.innerHTML = document.body.dataset.theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        });
    </script>
</body>

</html>