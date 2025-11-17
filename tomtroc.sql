-- Crée la base si elle n'existe pas
CREATE DATABASE IF NOT EXISTS tomtroc
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE tomtroc;

-- On supprime les tables si elles existent déjà
DROP TABLE IF EXISTS livre;
DROP TABLE IF EXISTS user;

-- ===========================================
-- TABLE : user
-- ===========================================
CREATE TABLE user (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLE : livre
-- ===========================================
CREATE TABLE livre (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  auteur VARCHAR(255) NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  description TEXT,
  dispo TINYINT(1) NOT NULL DEFAULT 1,
  user_id INT UNSIGNED NOT NULL,
  CONSTRAINT fk_livre_user
    FOREIGN KEY (user_id) REFERENCES user(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- DONNÉES DES UTILISATEURS
-- ===========================================

-- Utilisateur Chris
INSERT INTO user (username, password) VALUES
('chris', 'chris');

SET @chris_id = LAST_INSERT_ID();

-- Utilisateur Alex
INSERT INTO user (username, password) VALUES
('alex', 'alex');

SET @alex_id = LAST_INSERT_ID();

-- ===========================================
-- LIVRES DE CHRIS
-- ===========================================
INSERT INTO livre (titre, auteur, image, description, dispo, user_id) VALUES
('Le Petit Prince', 'Antoine de Saint-Exupéry', '/img/petitprince.jpg',
 'Un classique de la littérature française.', 1, @chris_id),

('1984', 'George Orwell', '/img/1984.jpg',
 'Roman dystopique sur une société de surveillance totale.', 0, @chris_id),

('Dune', 'Frank Herbert', '/img/dune.jpg',
 'Grande fresque de science-fiction sur la planète Arrakis.', 1, @chris_id);

-- ===========================================
-- LIVRES D'ALEX
-- ===========================================
INSERT INTO livre (titre, auteur, image, description, dispo, user_id) VALUES
('Harry Potter à l\'école des sorciers', 'J.K. Rowling', '/img/hp1.jpg',
 'Premier tome de la série Harry Potter.', 1, @alex_id),

('Le Hobbit', 'J.R.R. Tolkien', '/img/hobbit.jpg',
 'L\'aventure de Bilbo avant Le Seigneur des Anneaux.', 1, @alex_id);
