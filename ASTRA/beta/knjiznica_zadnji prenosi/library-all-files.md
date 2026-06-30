# 📚 KNJIŽNICA MODUL - COMPLETE DOWNLOAD PACKAGE

**Version:** 1.0  
**Date:** 2025-11-29  
**Total Files:** 24  

---

## 📋 INSTRUCTIONS

1. **Download this file** (click download button)
2. **Copy each section** into a new file with the exact filename
3. **Maintain the directory structure** as shown
4. **Import schema.sql** first
5. **Configure config.php** with your database credentials

---

## 📁 DIRECTORY STRUCTURE

```
/library-module/
├── /backend/
│   ├── config.php
│   ├── auth.php
│   ├── books.php
│   ├── chapters.php
│   ├── search.php
│   ├── settings.php
│   └── export.php
├── /frontend/
│   ├── login.html
│   ├── register.html
│   ├── index.html
│   ├── reader.html
│   ├── editor.html
│   ├── profile.html
│   ├── 404.html
│   ├── /css/
│   │   ├── library.css
│   │   ├── reader.css
│   │   └── editor.css
│   └── /js/
│       ├── library.js
│       ├── reader.js
│       ├── editor.js
│       └── speech.js
├── /storage/
│   └── /books/ (empty - will be auto-created)
├── /database/
│   └── schema.sql
├── DOKUMENTACIJA.md
└── README.md
```

---

# ==========================================
# DATABASE
# ==========================================

## FILE: database/schema.sql

```sql
-- ==========================================
-- KNJIŽNICA MODUL - DATABASE SCHEMA
-- Version: 1.0
-- Date: 2025-11-29
-- ==========================================

-- Create database (if needed)
CREATE DATABASE IF NOT EXISTS orakleum_orakleum 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE orakleum_orakleum;

-- ==========================================
-- USERS TABLE
-- ==========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- BOOKS TABLE
-- ==========================================
CREATE TABLE IF NOT EXISTS books (
    id VARCHAR(36) PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    cover_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_public (is_public),
    INDEX idx_created (created_at),
    FULLTEXT idx_search (title, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- CHAPTERS TABLE
-- ==========================================
CREATE TABLE IF NOT EXISTS chapters (
    id VARCHAR(36) PRIMARY KEY,
    book_id VARCHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content_file VARCHAR(255) NOT NULL,
    chapter_order INT NOT NULL,
    word_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_book (book_id),
    INDEX idx_order (book_id, chapter_order),
    FULLTEXT idx_title_search (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- READING PROGRESS TABLE
-- ==========================================
CREATE TABLE IF NOT EXISTS reading_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id VARCHAR(36) NOT NULL,
    chapter_id VARCHAR(36),
    last_position INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_user_book (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE SET NULL,
    INDEX idx_user_book (user_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- USER SETTINGS TABLE
-- ==========================================
CREATE TABLE IF NOT EXISTS user_settings (
    user_id INT PRIMARY KEY,
    
    -- Reading settings
    font_size INT DEFAULT 16,
    font_family VARCHAR(50) DEFAULT 'Arial',
    line_height DECIMAL(3,1) DEFAULT 1.6,
    theme VARCHAR(20) DEFAULT 'light',
    
    -- TTS settings
    tts_rate DECIMAL(3,1) DEFAULT 1.0,
    tts_pitch DECIMAL(3,1) DEFAULT 1.0,
    tts_volume DECIMAL(3,1) DEFAULT 1.0,
    tts_voice VARCHAR(100),
    
    -- Editor settings
    auto_save BOOLEAN DEFAULT TRUE,
    auto_save_interval INT DEFAULT 30,
    
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- INSERT DEFAULT TEST USER
-- ==========================================
INSERT INTO users (email, username, password_hash) VALUES 
('test@library.com', 'TestUser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

SET @test_user_id = LAST_INSERT_ID();
INSERT INTO user_settings (user_id) VALUES (@test_user_id);

INSERT INTO books (id, user_id, title, description, is_public) VALUES 
('550e8400-e29b-41d4-a716-446655440000', @test_user_id, 'Testna Knjiga', 'To je testna knjiga za razvoj.', TRUE);

INSERT INTO chapters (id, book_id, title, content_file, chapter_order, word_count) VALUES
('chapter-001', '550e8400-e29b-41d4-a716-446655440000', 'Poglavje 1: Uvod', 'chapter-1.html', 1, 250),
('chapter-002', '550e8400-e29b-41d4-a716-446655440000', 'Poglavje 2: Razvoj', 'chapter-2.html', 2, 450);
```

---

⚠️ **IMPORTANT:** Due to message length limits, I cannot include all 24 files in one artifact.

Instead, I'll provide you with the **best solution**:

---

## 🎯 BEST SOLUTION - SHARE THIS CONVERSATION

1. **Click the ⋮ (three dots)** next to this conversation title
2. **Select "Publish to web"** or **"Share"**
3. **Copy the public link**
4. **Open that link on your other computer**
5. **All artifacts will be accessible there!**

You can then:
- View each artifact
- Click "Download" on each one
- Copy code directly

---

## 📦 ALTERNATIVE - I CAN CREATE MULTIPLE DOWNLOADS

I can create **separate download packages** for:

1. **Package 1:** Database + Backend (7 files)
2. **Package 2:** Frontend HTML (7 files)  
3. **Package 3:** Frontend JS + CSS (7 files)
4. **Package 4:** Documentation (2 files)

**Would you like me to create these 4 separate packages?**

Or use the **Share Conversation** method above (recommended)! ✨

---

Let me know which you prefer! 🚀
