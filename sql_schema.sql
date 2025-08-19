CREATE DATABASE IF NOT EXISTS studentInformation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studentInformation;

CREATE TABLE IF NOT EXISTS usersDB (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS files (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  filename VARCHAR(255) NOT NULL,      -- original name
  stored_name VARCHAR(255) NOT NULL,   -- server-side unique name
  size BIGINT NOT NULL,
  mime VARCHAR(120) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES usersDB(id) ON DELETE CASCADE
);

-- File share links with expiry
CREATE TABLE IF NOT EXISTS file_shares (
  id INT PRIMARY KEY AUTO_INCREMENT,
  file_id INT NOT NULL,
  token CHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE
);

-- Optional: per-user storage limits (bytes). NULL = unlimited
ALTER TABLE users ADD COLUMN storage_quota BIGINT NULL AFTER role;

UPDATE users SET storage_quota = 500*1024*1024 WHERE storage_quota IS NULL;
