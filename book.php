<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Get book id from URL
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Log as recently read if user is logged in
if (isset($_SESSION['user_id']) && $book_id > 0) {
    $user_id = intval($_SESSION['user_id']);
    $conn->query("DELETE FROM recently_read WHERE user_id=$user_id AND book_id=$book_id");
    $conn->query("INSERT INTO recently_read (user_id, book_id, read_at) VALUES ($user_id, $book_id, NOW())");
}

// Get book details
$result = $conn->query("SELECT * FROM books WHERE id=$book_id");
$book = $result->fetch_assoc();
if (!$book) {
    echo "Book not found.";
    exit;
}

// Get chapters for this book
$chapters = [];
$chapterResult = $conn->query("SELECT * FROM chapters WHERE book_id=$book_id ORDER BY chapter_number ASC");
while ($row = $chapterResult->fetch_assoc()) {
    $chapters[] = $row;
}

// Check if book is favorite
$isFavorite = false;
if (isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $favCheck = $conn->query("SELECT 1 FROM favorites WHERE user_id=$userId AND book_id=$book_id");
    $isFavorite = $favCheck->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($book['title']); ?> - Book Details</title>
    <style>
        /* CSS Variables for theming */
        :root {
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --accent-primary: #9c6b3e;
            --accent-hover: #8a5d34;
            --border-color: #e9ecef;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-medium: 0 4px 20px rgba(0,0,0,0.12);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.dark-mode {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --bg-card: #2d2d2d;
            --text-primary: #e0e0e0;
            --text-secondary: #a0a0a0;
            --accent-primary: #c88d5a;
            --accent-hover: #d4a673;
            --border-color: #404040;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.3);
            --shadow-medium: 0 4px 20px rgba(0,0,0,0.4);
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
                Ubuntu, Cantarell, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: var(--transition);
        }

        /* Header */
        .header {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-light);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header > div {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header form button {
            background: var(--accent-primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .header form button:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        /* Main container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Book hero section */
        .content-wrapper {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .book-image {
            position: sticky;
            top: 120px;
            height: fit-content;
        }

        .book-image img {
            width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
            transition: var(--transition);
        }

        .book-image img:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .book-description {
            background: var(--bg-card);
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .book-description h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .book-description h3 {
            color: var(--text-secondary);
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 2rem;
        }

        .book-description p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .read-full-btn, .favorite-btn {
            padding: 0.875rem 1.75rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .read-full-btn {
            background: var(--accent-primary);
            color: white;
        }

        .read-full-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .favorite-btn {
            background: transparent;
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }

        .favorite-btn:hover {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
            transform: translateY(-2px);
        }

        /* Full book reader */
        .full-book-reader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--bg-primary);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .book-reader-header {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-light);
        }

        .book-reader-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .reader-controls {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .dark-mode-toggle label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .close-reader-btn {
            background: var(--accent-primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .close-reader-btn:hover {
            background: var(--accent-hover);
        }

        .book-content {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            scroll-behavior: smooth;
        }

        .chapter-container {
            max-width: 800px;
            margin: 0 auto 3rem auto;
            background: var(--bg-card);
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .chapter-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--accent-primary);
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--accent-primary);
        }

        .chapter-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-primary);
        }

        .chapter-text p {
            margin-bottom: 1.5rem;
        }

        /* Chapter navigation */
        .chapter-navigation {
            position: fixed;
            right: 2rem;
            top: 50%;
            transform: translateY(-50%);
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            box-shadow: var(--shadow-light);
            max-height: 60vh;
            overflow-y: auto;
            z-index: 1001;
            min-width: 200px;
        }

        .chapter-nav-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chapter-nav-item {
            display: block;
            padding: 0.5rem 0.75rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .chapter-nav-item:hover {
            background: var(--accent-primary);
            color: white;
        }

        .chapter-nav-item.active {
            background: var(--accent-primary);
            color: white;
        }

        /* Image viewer */
        .image-container {
            margin: 2rem 0;
            text-align: center;
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
        }

        .chapter-image {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            cursor: zoom-in;
            transition: var(--transition);
            box-shadow: var(--shadow-light);
        }

        .chapter-image:hover {
            transform: scale(1.02);
        }

        .chapter-image.zoomed {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 95vw;
            max-height: 95vh;
            z-index: 2000;
            cursor: zoom-out;
            box-shadow: var(--shadow-medium);
            border-radius: var(--border-radius);
        }

        /* Dark mode toggle button */
        .dark-mode-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1001;
        }

        .dark-mode-toggle button {
            background: var(--bg-card);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            box-shadow: var(--shadow-light);
        }

        .dark-mode-toggle button:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        /* No chapters message */
        .no-chapters {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Responsive design */
        @media (max-width: 1024px) {
            .content-wrapper {
                grid-template-columns: 300px 1fr;
                gap: 2rem;
            }

            .chapter-navigation {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .content-wrapper {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .book-image {
                position: static;
                max-width: 300px;
                margin: 0 auto;
            }

            .book-description {
                padding: 2rem;
            }

            .book-description h1 {
                font-size: 2rem;
                text-align: center;
            }

            .book-description h3 {
                text-align: center;
            }

            .action-buttons {
                flex-direction: column;
            }

            .read-full-btn, .favorite-btn {
                width: 100%;
                justify-content: center;
            }

            .book-reader-header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .reader-controls {
                flex-direction: column;
                gap: 1rem;
            }

            .book-content {
                padding: 1rem;
            }

            .chapter-container {
                padding: 1.5rem;
            }

            .dark-mode-toggle {
                bottom: 1rem;
                right: 1rem;
            }

            .chapter-navigation {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .header > div {
                padding: 1rem;
            }

            .book-description h1 {
                font-size: 1.75rem;
            }

            .chapter-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div>
            <div></div>
            <form action="1.php" method="get">
                <button type="submit" aria-label="Back to Home">🏠 Back to Home</button>
            </form>
        </div>
    </header>
    
    <div class="container">
        <main class="main-content">
            <div class="content-wrapper">
                <div class="book-image">
                    <img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?> Cover" />
                </div>
                
                <div class="book-description">
                    <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                    <h3><?php echo htmlspecialchars($book['author']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                    
                    <div class="action-buttons">
                        <button onclick="toggleFullBook()" class="read-full-btn" id="readFullBtn" aria-expanded="false" aria-controls="fullBookContent">
                            📚 Read Full Book
                        </button>
            
                            </form>
                    </div>
                </div>
            </div>
            
            <!-- Full Book Reader -->
            <div id="fullBookContent" class="full-book-reader" style="display: none;">
                <!-- Reading Progress Indicator -->
                <div class="scroll-indicator" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                    <div class="scroll-progress" id="scrollProgress"></div>
                </div>
                
                <div class="book-reader-header">
                    <h2>📖 <?php echo htmlspecialchars($book['title']); ?></h2>
                    <div class="reader-controls">
                        <label class="dark-mode-toggle">
                            <input type="checkbox" id="darkModeToggleReader" onchange="toggleDarkMode()" aria-label="Toggle dark mode"/>
                            <span>Dark Mode</span>
                        </label>
                        <button onclick="toggleFullBook()" class="close-reader-btn" aria-label="Close book reader">✕ Close</button>
                    </div>
                </div>
                
                <!-- Chapter Navigation -->
                <?php if (!empty($chapters)): ?>
                <nav class="chapter-navigation" id="chapterNavigation" aria-label="Chapter navigation">
                    <div class="chapter-nav-title">Chapters</div>
                    <?php foreach ($chapters as $index => $chapter): ?>
                        <div class="chapter-nav-item" role="button" tabindex="0" onclick="scrollToChapter(<?php echo $chapter['chapter_number']; ?>)" onkeypress="if(event.key==='Enter'){scrollToChapter(<?php echo $chapter['chapter_number']; ?>)}" >
                            <?php echo htmlspecialchars($chapter['title']); ?>
                        </div>
                    <?php endforeach; ?>
                </nav>
                <?php endif; ?>
                
                <section class="book-content" id="bookContent" tabindex="0">
                    <?php if (!empty($chapters)): ?>
                        <?php foreach ($chapters as $index => $chapter): ?>
                            <article class="chapter-container" id="chapter-<?php echo $chapter['chapter_number']; ?>" tabindex="-1" aria-labelledby="chapter-title-<?php echo $chapter['chapter_number']; ?>">
                                <h3 class="chapter-title" id="chapter-title-<?php echo $chapter['chapter_number']; ?>"><?php echo htmlspecialchars($chapter['title']); ?></h3>
                                <div class="chapter-text">
                                    <?php if ($chapter['chapter_type'] === 'image'): ?>
                                        <div class="image-viewer-container">
                                            <?php 
                                            $imageUrls = explode("\n", $chapter['content']);
                                            foreach ($imageUrls as $imgIndex => $imageUrl): 
                                                if (!empty(trim($imageUrl))): ?>
                                                    <div class="image-container">
                                                        <img src="<?php echo htmlspecialchars(trim($imageUrl)); ?>" 
                                                             alt="Chapter Image" 
                                                             class="chapter-image"
                                                             onclick="toggleZoom(this)"
                                                             role="button"
                                                             tabindex="0"
                                                             onkeypress="if(event.key==='Enter'){toggleZoom(this)}">
                                                    </div>
                                                <?php endif;
                                            endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <?php echo nl2br(htmlspecialchars($chapter['content'])); ?>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-chapters" role="alert" aria-live="polite">
                            <p>📚 This book doesn't have chapters yet or is still being added to our library.</p>
                            <p>Please check back later!</p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>
    
    <div class="dark-mode-toggle">
        <button id="darkModeToggle" onclick="toggleDarkMode()" aria-pressed="false" aria-label="Toggle dark mode">🌙 Dark Mode</button>
    </div>
    
    <script>
        // Book reader functionality with enhanced scrolling and scroll function added
        function toggleFullBook() {
            const fullBookContent = document.getElementById('fullBookContent');
            const readFullBtn = document.getElementById('readFullBtn');
            
            if (fullBookContent.style.display === 'none' || fullBookContent.style.display === '') {
                fullBookContent.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                readFullBtn.textContent = '📚 Close Book';
                readFullBtn.setAttribute('aria-expanded', 'true');
                
                // Auto-scroll to top of book content with smooth animation
                setTimeout(() => {
                    const bookContent = document.getElementById('bookContent');
                    bookContent.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    
                    // Initialize scroll tracking
                    initializeScrollTracking();
                    updateChapterNavigation();
                }, 100);
                
                <?php if (isset($_SESSION['id'])): ?>
                fetch('track_reading.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        book_id: <?php echo $book['id']; ?>,
                        action: 'start_reading'
                    })
                });
                <?php endif; ?>
            } else {
                fullBookContent.style.display = 'none';
                document.body.style.overflow = 'auto';
                readFullBtn.textContent = '📚 Read Full Book';
                readFullBtn.setAttribute('aria-expanded', 'false');
                
                // Clean up scroll tracking
                cleanupScrollTracking();
            }
        }
        
        // Chapter navigation with smooth scrolling
        function scrollToChapter(chapterNumber) {
            const chapterElement = document.getElementById(`chapter-${chapterNumber}`);
            const bookContent = document.getElementById('bookContent');
            
            if (chapterElement && bookContent) {
                const offsetTop = chapterElement.offsetTop;
                
                bookContent.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
                
                // Update active chapter in navigation
                updateActiveChapter(chapterNumber);
            }
        }
        
        // Initialize scroll tracking and progress indicator
        function initializeScrollTracking() {
            const bookContent = document.getElementById('bookContent');
            const scrollProgress = document.getElementById('scrollProgress');
            
            if (bookContent && scrollProgress) {
                bookContent.addEventListener('scroll', handleScroll);
            }
        }
        
        // Clean up scroll event listeners
        function cleanupScrollTracking() {
            const bookContent = document.getElementById('bookContent');
            if (bookContent) {
                bookContent.removeEventListener('scroll', handleScroll);
            }
        }
        
        // Handle scroll events for progress indicator and chapter navigation
        function handleScroll() {
            updateScrollProgress();
            updateChapterNavigation();
        }
        
        // Update reading progress indicator
        function updateScrollProgress() {
            const bookContent = document.getElementById('bookContent');
            const scrollProgress = document.getElementById('scrollProgress');
            
            if (bookContent && scrollProgress) {
                const scrollTop = bookContent.scrollTop;
                const scrollHeight = bookContent.scrollHeight - bookContent.clientHeight;
                const progress = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
                
                scrollProgress.style.width = `${Math.min(progress, 100)}%`;
                // Update aria-valuenow for accessibility
                const indicator = scrollProgress.parentNode;
                if(indicator) indicator.setAttribute('aria-valuenow', Math.round(progress));
            }
        }
        
        // Update active chapter in navigation based on scroll position
        function updateChapterNavigation() {
            const bookContent = document.getElementById('bookContent');
            const chapterContainers = document.querySelectorAll('.chapter-container');
            const navItems = document.querySelectorAll('.chapter-nav-item');
            
            if (!bookContent || chapterContainers.length === 0) return;
            
            const scrollTop = bookContent.scrollTop;
            
            let activeChapterIndex = 0;
            
            // Find the currently visible chapter (using offsetTop relative to bookContent)
            chapterContainers.forEach((chapter, index) => {
                const chapterTop = chapter.offsetTop;
                const chapterBottom = chapterTop + chapter.offsetHeight;
                
                if (scrollTop >= chapterTop - 50 && scrollTop < chapterBottom - 50) { 
                    activeChapterIndex = index;
                }
            });
            
            // Update navigation highlighting
            navItems.forEach((item, index) => {
                if (index === activeChapterIndex) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }
        
        // Update active chapter manually (when clicking navigation)
        function updateActiveChapter(chapterNumber) {
            const navItems = document.querySelectorAll('.chapter-nav-item');
            const chapterContainers = document.querySelectorAll('.chapter-container');
            
            // Find the index of the target chapter
            let targetIndex = -1;
            chapterContainers.forEach((container, index) => {
                if (container.id === `chapter-${chapterNumber}`) {
                    targetIndex = index;
                }
            });
            
            // Update navigation highlighting
            navItems.forEach((item, index) => {
                if (index === targetIndex) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }
        
        // Image zoom functionality
        function toggleZoom(img) {
            if (img.classList.contains('zoomed')) {
                img.classList.remove('zoomed');
                document.body.style.overflow = 'hidden'; // Keep reader overflow hidden
            } else {
                // First remove zoom from any other images
                document.querySelectorAll('.chapter-image.zoomed').forEach(zoomedImg => {
                    zoomedImg.classList.remove('zoomed');
                });
                img.classList.add('zoomed');
            }
        }
        
        // Close zoom when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('zoomed')) {
                return; // Don't close if clicking on the zoomed image itself
            }
            
            const zoomedImages = document.querySelectorAll('.chapter-image.zoomed');
            if (zoomedImages.length > 0 && !e.target.closest('.chapter-image')) {
                zoomedImages.forEach(img => {
                    img.classList.remove('zoomed');
                });
            }
        });
        
        // Dark mode toggle
        function toggleDarkMode() {
            const body = document.body;
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeToggleReader = document.getElementById('darkModeToggleReader');
            
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                darkModeToggle.innerHTML = '🌙 Dark Mode';
                darkModeToggle.setAttribute('aria-pressed', 'false');
                if (darkModeToggleReader) {
                    darkModeToggleReader.checked = false;
                }
                localStorage.setItem('darkMode', 'disabled');
            } else {
                body.classList.add('dark-mode');
                darkModeToggle.innerHTML = '☀️ Light Mode';
                darkModeToggle.setAttribute('aria-pressed', 'true');
                if (darkModeToggleReader) {
                    darkModeToggleReader.checked = true;
                }
                localStorage.setItem('darkMode', 'enabled');
            }
        }
        
        // Check for saved preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            const toggler = document.getElementById('darkModeToggle');
            if(toggler) {
                toggler.innerHTML = '☀️ Light Mode';
                toggler.setAttribute('aria-pressed', 'true');
            }
            const readerToggle = document.getElementById('darkModeToggleReader');
            if (readerToggle) readerToggle.checked = true;
        }
        
        // Enhanced keyboard navigation
        document.addEventListener('keydown', function(e) {
            const fullBookContent = document.getElementById('fullBookContent');
            const bookContent = document.getElementById('bookContent');
            
            if (e.key === 'Escape') {
                // Close zoomed images first
                const zoomedImages = document.querySelectorAll('.chapter-image.zoomed');
                if (zoomedImages.length > 0) {
                    zoomedImages.forEach(img => {
                        img.classList.remove('zoomed');
                    });
                } 
                // Then close reader if still open
                else if (fullBookContent.style.display === 'flex') {
                    toggleFullBook();
                }
            }
            
            // Chapter navigation with arrow keys (only when reader is open)
            if (fullBookContent.style.display === 'flex' && bookContent) {
                const chapterContainers = document.querySelectorAll('.chapter-container');
                const currentScroll = bookContent.scrollTop;
                let targetChapter = null;
                
                if (e.key === 'ArrowDown' && e.ctrlKey) {
                    e.preventDefault();
                    // Find next chapter
                    chapterContainers.forEach((chapter, index) => {
                        if (chapter.offsetTop > currentScroll + 100 && !targetChapter) {
                            targetChapter = chapter;
                        }
                    });
                    
                    if (targetChapter) {
                        const chapterNumber = targetChapter.id.replace('chapter-', '');
                        scrollToChapter(parseInt(chapterNumber));
                    }
                }
                
                if (e.key === 'ArrowUp' && e.ctrlKey) {
                    e.preventDefault();
                    // Find previous chapter
                    const chapters = Array.from(chapterContainers).reverse();
                    chapters.forEach((chapter, index) => {
                        if (chapter.offsetTop < currentScroll - 100 && !targetChapter) {
                            targetChapter = chapter;
                        }
                    });
                    
                    if (targetChapter) {
                        const chapterNumber = targetChapter.id.replace('chapter-', '');
                        scrollToChapter(parseInt(chapterNumber));
                    }
                }
                
                // Smooth scroll up/down with Page Up/Down
                if (e.key === 'PageDown') {
                    e.preventDefault();
                    bookContent.scrollBy({
                        top: bookContent.clientHeight * 0.8,
                        behavior: 'smooth'
                    });
                }
                
                if (e.key === 'PageUp') {
                    e.preventDefault();
                    bookContent.scrollBy({
                        top: -bookContent.clientHeight * 0.8,
                        behavior: 'smooth'
                    });
                }
                
                // Home/End navigation
                if (e.key === 'Home' && e.ctrlKey) {
                    e.preventDefault();
                    bookContent.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
                
                if (e.key === 'End' && e.ctrlKey) {
                    e.preventDefault();
                    bookContent.scrollTo({
                        top: bookContent.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            }
        });
        
        // Auto-hide chapter navigation on mobile scroll
        let scrollTimeout;
        function handleMobileScroll() {
            const chapterNav = document.getElementById('chapterNavigation');
            if (chapterNav && window.innerWidth <= 1024) {
                chapterNav.style.opacity = '0.3';
                
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    chapterNav.style.opacity = '1';
                }, 1000);
            }
        }
        
        // Add mobile scroll handling
        document.addEventListener('DOMContentLoaded', function() {
            const bookContent = document.getElementById('bookContent');
            if (bookContent) {
                bookContent.addEventListener('scroll', handleMobileScroll);
            }
        });
        
        // Page transition for links
        document.querySelectorAll('a').forEach(function(link) {
            if (link.hostname === window.location.hostname && 
                link.target !== "_blank" && 
                !link.href.startsWith('javascript:')) {
                link.addEventListener('click', function(e) {
                    if (link.hash && link.pathname === window.location.pathname) return;
                    e.preventDefault();
                    document.body.classList.add('fade-out');
                    setTimeout(function() {
                        window.location = link.href;
                    }, 500);
                });
            }
        });
        
        // Reading time estimation (optional feature)
        function estimateReadingTime() {
            const chapterContainers = document.querySelectorAll('.chapter-container');
            let totalWords = 0;
            
            chapterContainers.forEach(container => {
                const textContent = container.querySelector('.chapter-text');
                if (textContent) {
                    const text = textContent.textContent || textContent.innerText;
                    const words = text.trim().split(/\s+/).length;
                    totalWords += words;
                }
            });
            
            // Average reading speed: 200-300 words per minute
            const readingSpeed = 250;
            const estimatedMinutes = Math.ceil(totalWords / readingSpeed);
            
            return {
                words: totalWords,
                minutes: estimatedMinutes,
                formatted: estimatedMinutes < 60 ? 
                    `${estimatedMinutes} min read` : 
                    `${Math.floor(estimatedMinutes / 60)}h ${estimatedMinutes % 60}m read`
            };
        }
        
        // Initialize reading time display when book is opened
        function displayReadingTime() {
            const bookReaderHeader = document.querySelector('.book-reader-header h2');
            if (bookReaderHeader) {
                const readingTime = estimateReadingTime();
                if (readingTime.words > 0) {
                    const timeSpan = document.createElement('span');
                    timeSpan.style.fontSize = '0.8em';
                    timeSpan.style.color = 'var(--text-secondary)';
                    timeSpan.style.fontWeight = 'normal';
                    timeSpan.style.marginLeft = '1rem';
                    timeSpan.textContent = `(${readingTime.formatted})`;
                    
                    // Remove existing time span if present
                    const existingTimeSpan = bookReaderHeader.querySelector('span');
                    if (existingTimeSpan) {
                        existingTimeSpan.remove();
                    }
                    
                    bookReaderHeader.appendChild(timeSpan);
                }
            }
        }
        
        // Enhanced book opening with reading time
        const originalToggleFullBook = toggleFullBook;
        toggleFullBook = function() {
            originalToggleFullBook();
            
            // Add reading time after opening
            setTimeout(() => {
                if (document.getElementById('fullBookContent').style.display === 'flex') {
                    displayReadingTime();
                }
            }, 200);
        };
    </script>
</body>
</html>

