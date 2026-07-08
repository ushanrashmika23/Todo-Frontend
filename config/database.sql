CREATE DATABASE todo_app;

USE todo_app;

-- ==========================
-- Users Table
-- ==========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    email VARCHAR(150) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================
-- Todos Table
-- ==========================
CREATE TABLE todos (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    title VARCHAR(200) NOT NULL,

    description TEXT,

    is_completed BOOLEAN DEFAULT FALSE,

    due_date DATE,

    priority ENUM('LOW', 'MEDIUM', 'HIGH') DEFAULT 'MEDIUM',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_todo_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


--============================

INSERT INTO users (name, email, password) VALUES
('John Doe', 'john@example.com', 'john123'),
('Alice Johnson', 'alice@example.com', 'alice123'),
('Bob Smith', 'bob@example.com', 'bob123'),
('Emma Wilson', 'emma@example.com', 'emma123'),
('Michael Brown', 'michael@example.com', 'michael123'),
('Sophia Davis', 'sophia@example.com', 'sophia123'),
('Daniel Taylor', 'daniel@example.com', 'daniel123'),
('Olivia Martin', 'olivia@example.com', 'olivia123');