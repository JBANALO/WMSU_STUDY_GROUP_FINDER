-- WMSU Study Group Finder Database Schema for Railway

CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(255) UNIQUE NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  first_name VARCHAR(255),
  last_name VARCHAR(255),
  password VARCHAR(255) NOT NULL,
  status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS study_groups (
  id INT PRIMARY KEY AUTO_INCREMENT,
  group_name VARCHAR(255) NOT NULL,
  description TEXT,
  subject VARCHAR(255),
  creator_id INT NOT NULL,
  status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
  decline_reason TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS group_members (
  id INT PRIMARY KEY AUTO_INCREMENT,
  group_id INT NOT NULL,
  user_id INT NOT NULL,
  role VARCHAR(50) DEFAULT 'member',
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (group_id) REFERENCES study_groups(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS group_messages (
  id INT PRIMARY KEY AUTO_INCREMENT,
  group_id INT NOT NULL,
  user_id INT NOT NULL,
  message TEXT,
  attachment VARCHAR(255),
  message_type VARCHAR(50) DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (group_id) REFERENCES study_groups(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS meetings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  group_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  meeting_date DATETIME NOT NULL,
  location VARCHAR(255),
  is_online BOOLEAN DEFAULT 1,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (group_id) REFERENCES study_groups(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS notifications (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  type VARCHAR(50),
  title VARCHAR(255),
  message TEXT,
  is_read BOOLEAN DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS user_last_seen (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  group_id INT NOT NULL,
  last_seen_message_id INT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (group_id) REFERENCES study_groups(id)
);
