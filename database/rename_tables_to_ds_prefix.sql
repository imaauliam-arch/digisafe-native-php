-- DIGISAFE: migrasi nama tabel lama ke prefix ds_
-- Jalankan SATU KALI pada database db_digisafe yang sudah berisi data.
-- Backup database terlebih dahulu melalui phpMyAdmin > Export.
USE db_digisafe;

RENAME TABLE
    categories TO ds_categories,
    posts TO ds_posts,
    tags TO ds_tags,
    post_tags TO ds_post_tags,
    comments TO ds_comments,
    contact_messages TO ds_contact_messages;

-- Verifikasi setelah migrasi:
-- SHOW TABLES;
-- SELECT COUNT(*) FROM ds_categories;
-- SELECT COUNT(*) FROM ds_posts;
-- SELECT COUNT(*) FROM ds_tags;
-- SELECT COUNT(*) FROM ds_post_tags;
-- SELECT COUNT(*) FROM ds_comments;
-- SELECT COUNT(*) FROM ds_contact_messages;
