-- Jalankan file ini HANYA jika database db_digisafe dari ZIP versi pertama
-- sudah pernah dibuat dan diimpor sebelumnya.
USE db_digisafe;

ALTER TABLE posts
    ADD COLUMN author_name VARCHAR(100) NOT NULL DEFAULT 'Admin DIGISAFE' AFTER content;

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    commenter VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    comment_body TEXT NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;
