-- Crée la base si elle n'existe pas
CREATE DATABASE IF NOT EXISTS tomtroc
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE tomtroc;

-- On supprime les tables si elles existent déjà
DROP TABLE IF EXISTS commentaire;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS conversation_user;
DROP TABLE IF EXISTS conversation;
DROP TABLE IF EXISTS livre;
DROP TABLE IF EXISTS user;

-- ===========================================
-- TABLE : user
-- ===========================================
CREATE TABLE user (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  image VARCHAR(255) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
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
-- TABLE : conversation
-- Une conversation (thread) qui regroupe des messages
-- ===========================================
CREATE TABLE conversation (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLE : conversation_user
-- Table de jointure N-N entre conversation et user
-- Un user peut participer à plusieurs conversations
-- Une conversation peut avoir 1..n users
-- ===========================================
CREATE TABLE conversation_user (
  conversation_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (conversation_id, user_id),

  CONSTRAINT fk_conv_user_conversation
    FOREIGN KEY (conversation_id) REFERENCES conversation(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT fk_conv_user_user
    FOREIGN KEY (user_id) REFERENCES user(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLE : message
-- Un message appartient à UNE conversation et UN user
-- ===========================================
CREATE TABLE message (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  conversation_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  read_at DATETIME DEFAULT NULL,

  CONSTRAINT fk_message_conversation
    FOREIGN KEY (conversation_id) REFERENCES conversation(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT fk_message_user
    FOREIGN KEY (user_id) REFERENCES user(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- TABLE : commentaire
-- (inchangée, toujours liée à user + livre)
-- ===========================================
CREATE TABLE commentaire (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  contenu TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  user_id INT UNSIGNED NOT NULL,
  livre_id INT UNSIGNED DEFAULT NULL,
  CONSTRAINT fk_commentaire_user
    FOREIGN KEY (user_id) REFERENCES user(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_commentaire_livre
    FOREIGN KEY (livre_id) REFERENCES livre(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- DONNÉES DES UTILISATEURS
-- ===========================================

-- Utilisateur Chris
INSERT INTO user (username, email, image, password, created_at) VALUES
('chris', 'chris@example.com', 'assets/chris.jpg', 'chris', '2023-04-12 10:15:00');

SET @chris_id = LAST_INSERT_ID();

-- Utilisateur Alex
INSERT INTO user (username, email, image, password, created_at) VALUES
('alex', 'alex@example.com', 'assets/alex.jpg', 'alex', '2023-06-03 18:40:00');

SET @alex_id = LAST_INSERT_ID();

-- ===========================================
-- LIVRES DE CHRIS (DESCRIPTIONS LONGUES)
-- ===========================================
INSERT INTO livre (titre, auteur, image, description, dispo, user_id) VALUES
(
  'Le Petit Prince',
  'Antoine de Saint-Exupéry',
  'assets/petitprince.jpg',
  '“Le Petit Prince” est un livre que j''ai relu plusieurs fois à différents moments de ma vie, et à chaque lecture j''y ai trouvé quelque chose de nouveau. Quand je l''ai découvert plus jeune, je l''ai surtout vécu comme un joli conte poétique. En le relisant adulte, certaines phrases m''ont littéralement arrêté dans ma lecture tant elles résonnaient avec mon quotidien, ma façon de voir les relations et ce que l''on considère comme important. Ce que j''aime particulièrement, c''est la manière dont le livre parle de l''amitié, de la responsabilité et du fait de prendre soin des autres sans jamais être lourd ou moralisateur. C''est une histoire courte, mais elle laisse une impression durable, un peu comme une petite lumière qui continue de briller longtemps après avoir refermé le livre.',
  1,
  @chris_id
),

(
  '1984',
  'George Orwell',
  'assets/1984.jpg',
  '“1984” a été une lecture assez éprouvante, mais dans le bon sens du terme. Dès les premières pages, j''ai ressenti une forme de malaise, cette impression d''être enfermé dans un monde où chaque geste, chaque pensée est surveillé. Le roman m''a fait réfléchir à la place de la liberté individuelle, à la manipulation de l''information et à la façon dont les régimes autoritaires peuvent déformer la réalité. Ce n''est pas un livre que l''on lit pour se détendre, mais plutôt un texte qui bouscule et qui reste en tête longtemps. Après l''avoir terminé, j''ai eu du mal à passer à autre chose : certaines scènes et certaines phrases reviennent encore régulièrement quand j''entends parler de politique, de médias ou de contrôle social. C''est un classique qui mérite clairement sa réputation, même si on ne ressort pas indemne de sa lecture.',
  0,
  @chris_id
),

(
  'Dune',
  'Frank Herbert',
  'assets/dune.jpg',
  '“Dune” est un véritable voyage, autant par son univers que par la profondeur de ses personnages. Au début, j''ai mis un peu de temps à entrer dans l''histoire, mais une fois plongé dans l''atmosphère d''Arrakis, j''ai eu du mal à lâcher le livre. Ce qui m''a le plus marqué, c''est la richesse du monde : les enjeux politiques, les luttes de pouvoir entre les maisons, la place de l''écologie, la relation au désert et à l''épice… tout est dense mais extrêmement bien construit. J''ai aussi beaucoup aimé suivre l''évolution de Paul et la manière dont il se retrouve pris entre son destin, les attentes des autres et ses propres doutes. C''est un livre qui demande un peu d''attention, mais qui récompense largement le lecteur avec une histoire épique, intelligente et fascinante.',
  1,
  @chris_id
);

-- ===========================================
-- LIVRES D'ALEX
-- ===========================================
INSERT INTO livre (titre, auteur, image, description, dispo, user_id) VALUES
(
  'Harry Potter à l''école des sorciers',
  'J.K. Rowling',
  'assets/hp1.jpg',
  'Ce premier tome de “Harry Potter” est l''un de ceux qui m''ont donné le goût de la lecture quand j''étais plus jeune...',
  1,
  @alex_id
),

(
  'Le Hobbit',
  'J.R.R. Tolkien',
  'assets/hobbit.jpg',
  '“Le Hobbit” a pour moi le charme d''un grand conte d''aventure...',
  1,
  @alex_id
);

-- ===========================================
-- COMMENTAIRES ENTRE CHRIS ET ALEX
-- ===========================================
INSERT INTO commentaire (contenu, user_id, livre_id)
VALUES ('Salut Alex, tu as déjà lu Dune ? Tu en as pensé quoi ?', @chris_id, 3);

INSERT INTO commentaire (contenu, user_id, livre_id)
VALUES ('Oui ! C’est un roman incroyable, super riche. Tu veux le lire ?', @alex_id, 3);

INSERT INTO commentaire (contenu, user_id, livre_id)
VALUES ('Grave ! Je suis super motivé. Il est toujours dispo à l''échange ?', @chris_id, 3);

INSERT INTO commentaire (contenu, user_id, livre_id)
VALUES ('Ouais il est dispo ! Si tu veux je peux te le passer cette semaine.', @alex_id, 3);
