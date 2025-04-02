CREATE TABLE users (
                       id SERIAL PRIMARY KEY,
                       name VARCHAR(100) NOT NULL,
                       email VARCHAR(100) UNIQUE NOT NULL,
                       password VARCHAR(255) NOT NULL,
                       phone VARCHAR(20),
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tours (
                       id SERIAL PRIMARY KEY,
                       name VARCHAR(255) NOT NULL,
                       description TEXT,
                       short_description VARCHAR(255),
                       price DECIMAL(10,2) NOT NULL,
                       location VARCHAR(100),
                       date DATE,
                       image VARCHAR(255),
                       featured BOOLEAN DEFAULT false,
                       category VARCHAR(50) CHECK (category IN ('cultural', 'festival', 'adaptation'))
);

CREATE TABLE bookings (
                          id SERIAL PRIMARY KEY,
                          user_id INTEGER REFERENCES users(id),
                          tour_id INTEGER REFERENCES tours(id),
                          booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          guests INTEGER DEFAULT 1,
                          status VARCHAR(20) DEFAULT 'pending'
);

CREATE TABLE favorites (
                           id SERIAL PRIMARY KEY,
                           user_id INTEGER REFERENCES users(id),
                           tour_id INTEGER REFERENCES tours(id),
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           UNIQUE(user_id, tour_id)
);