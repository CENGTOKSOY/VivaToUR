-- database/seed.sql
INSERT INTO tours (name, description, price) VALUES
                                                 ('Kapadokya Turu', 'Kapadokya balon turu ve peri bacaları', 1500.00),
                                                 ('Ege Turu', 'Ege bölgesi tarihi ve deniz turu', 2000.00);

INSERT INTO admin_users (username, password_hash) VALUES
    ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- şifre: "password"