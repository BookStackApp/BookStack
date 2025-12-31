-- BookStack Test Data Seed
-- Creates sample books, chapters, and pages for migration testing

USE bookstack;

-- Test user
INSERT INTO users (id, name, email, password, created_at, updated_at) VALUES
(1, 'Test Admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- Test books
INSERT INTO books (id, name, slug, description, created_at, updated_at, created_by, updated_by, owned_by) VALUES
(1, 'Migration Test Book', 'migration-test-book', 'This is a test book for migration', NOW(), NOW(), 1, 1, 1),
(2, 'Technical Documentation', 'technical-documentation', 'System technical docs', NOW(), NOW(), 1, 1, 1),
(3, 'User Guides', 'user-guides', 'End user documentation', NOW(), NOW(), 1, 1, 1);

-- Test chapters
INSERT INTO chapters (id, book_id, name, slug, description, priority, created_at, updated_at, created_by, updated_by, owned_by) VALUES
(1, 1, 'Getting Started', 'getting-started', 'Introduction chapter', 0, NOW(), NOW(), 1, 1, 1),
(2, 1, 'Advanced Topics', 'advanced-topics', 'Deep dive into features', 1, NOW(), NOW(), 1, 1, 1),
(3, 2, 'Architecture', 'architecture', 'System architecture docs', 0, NOW(), NOW(), 1, 1, 1);

-- Test pages
INSERT INTO pages (id, book_id, chapter_id, name, slug, html, text, priority, created_at, updated_at, created_by, updated_by, owned_by, draft, template, revision_count, editor) VALUES
(1, 1, 1, 'Welcome Page', 'welcome-page', 
  '<h1>Welcome to Migration Test</h1><p>This is a test page with <strong>bold</strong> and <em>italic</em> text.</p><ul><li>Item 1</li><li>Item 2</li><li>Item 3</li></ul>',
  'Welcome to Migration Test This is a test page with bold and italic text. Item 1 Item 2 Item 3',
  0, NOW(), NOW(), 1, 1, 1, 0, 0, 1, 'wysiwyg'),

(2, 1, 1, 'Installation Guide', 'installation-guide',
  '<h1>Installation</h1><p>Follow these steps:</p><ol><li>Download the package</li><li>Extract files</li><li>Run installer</li></ol><pre><code>sudo apt-get install package</code></pre>',
  'Installation Follow these steps: 1. Download the package 2. Extract files 3. Run installer sudo apt-get install package',
  1, NOW(), NOW(), 1, 1, 1, 0, 0, 1, 'wysiwyg'),

(3, 1, 2, 'Advanced Configuration', 'advanced-configuration',
  '<h1>Advanced Configuration</h1><h2>Database Setup</h2><p>Configure your database connection:</p><code>DB_HOST=localhost</code><h2>Security</h2><p>Important security settings.</p>',
  'Advanced Configuration Database Setup Configure your database connection: DB_HOST=localhost Security Important security settings.',
  0, NOW(), NOW(), 1, 1, 1, 0, 0, 1, 'wysiwyg'),

(4, 1, NULL, 'Standalone Page', 'standalone-page',
  '<h1>This is a standalone page</h1><p>Not in any chapter, directly under book.</p>',
  'This is a standalone page Not in any chapter, directly under book.',
  10, NOW(), NOW(), 1, 1, 1, 0, 0, 1, 'wysiwyg'),

(5, 2, 3, 'System Architecture', 'system-architecture',
  '<h1>System Architecture</h1><h2>Components</h2><ul><li>Frontend: React</li><li>Backend: Laravel</li><li>Database: MySQL</li></ul><h2>Diagrams</h2><p>See attached diagrams.</p>',
  'System Architecture Components Frontend: React Backend: Laravel Database: MySQL Diagrams See attached diagrams.',
  0, NOW(), NOW(), 1, 1, 1, 0, 0, 1, 'wysiwyg'),

(6, 3, NULL, 'Quick Start Guide', 'quick-start-guide',
  '<h1>Quick Start</h1><p>Get up and running in 5 minutes:</p><ol><li>Create account</li><li>Login</li><li>Start creating content</li></ol>',
  'Quick Start Get up and running in 5 minutes: 1. Create account 2. Login 3. Start creating content',
  0, NOW(), NOW(), 1, 1, 1, 0, 0, 1, 'wysiwyg');

-- Set AUTO_INCREMENT values
ALTER TABLE books AUTO_INCREMENT = 10;
ALTER TABLE chapters AUTO_INCREMENT = 10;
ALTER TABLE pages AUTO_INCREMENT = 10;
ALTER TABLE users AUTO_INCREMENT = 10;

-- Grant permissions
GRANT ALL PRIVILEGES ON bookstack.* TO 'bookstack'@'%';
FLUSH PRIVILEGES;
