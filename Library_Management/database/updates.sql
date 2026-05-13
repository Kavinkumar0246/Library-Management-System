USE library_management;

-- 1. Updates to users table
ALTER TABLE users ADD COLUMN reading_streak INT DEFAULT 0;
ALTER TABLE users ADD COLUMN last_login_date DATE NULL;

-- 2. Updates to books table
ALTER TABLE books ADD COLUMN department VARCHAR(100) DEFAULT 'General';
ALTER TABLE books ADD COLUMN block VARCHAR(10) DEFAULT 'A';
ALTER TABLE books ADD COLUMN row_num INT DEFAULT 1;
ALTER TABLE books ADD COLUMN column_num INT DEFAULT 1;

-- 3. Create eresources table
CREATE TABLE eresources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    type ENUM('ebook', 'newspaper', 'journal', 'other'),
    link VARCHAR(255),
    added_on DATE
);

-- Seed some eresources
INSERT INTO eresources (title, type, link, added_on) VALUES
('The New York Times', 'newspaper', 'https://nytimes.com', CURDATE()),
('Advanced Web Architecture', 'ebook', 'https://example.com/ebook.pdf', CURDATE());

-- Seed some books location updates
UPDATE books SET department='Computer Science', block='B', row_num=2, column_num=4;
