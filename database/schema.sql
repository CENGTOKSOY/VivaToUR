-- Turlar tablosu
CREATE TABLE IF NOT EXISTS tours (
                                     id SERIAL PRIMARY KEY,
                                     name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- Admin kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS admin_users (
                                           id SERIAL PRIMARY KEY,
                                           username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );