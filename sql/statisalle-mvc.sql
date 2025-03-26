-- Table type_utilisateur
CREATE TABLE IF NOT EXISTS type_utilisateur (
id_type INT AUTO_INCREMENT PRIMARY KEY,
nom_type VARCHAR(50) NOT NULL
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Table employe
CREATE TABLE IF NOT EXISTS employe (
id_employe CHAR(7) PRIMARY KEY,
nom VARCHAR(50) NOT NULL,
prenom VARCHAR(50) NOT NULL,
telephone CHAR(10)
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table login
CREATE TABLE IF NOT EXISTS login (
id_login INT AUTO_INCREMENT PRIMARY KEY,
login VARCHAR(25) NOT NULL,
mdp VARCHAR(255) NOT NULL,
api_key VARCHAR(12),
id_type INT,
id_employe CHAR(7),
FOREIGN KEY (id_type) REFERENCES type_utilisateur(id_type),
FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Table salle
CREATE TABLE IF NOT EXISTS salle (
id_salle INT AUTO_INCREMENT PRIMARY KEY,
nom VARCHAR(50) NOT NULL,
capacite INT NOT NULL,
videoproj BOOLEAN,
ecran_xxl BOOLEAN,
ordinateur INT DEFAULT 0,
type VARCHAR(50),
logiciels VARCHAR(255),
imprimante BOOLEAN
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Table activite
CREATE TABLE IF NOT EXISTS activite (
id_activite CHAR(8) PRIMARY KEY,
nom_activite VARCHAR(50) NOT NULL
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Table reservation
CREATE TABLE IF NOT EXISTS reservation (
id_reservation CHAR(7) PRIMARY KEY,
id_salle INT NOT NULL,
id_employe CHAR(7) NOT NULL,
id_activite CHAR(8) NOT NULL,
date_reservation DATE NOT NULL,
heure_debut TIME NOT NULL,
heure_fin TIME NOT NULL,
FOREIGN KEY (id_salle) REFERENCES salle(id_salle),
FOREIGN KEY (id_employe) REFERENCES employe(id_employe),
FOREIGN KEY (id_activite) REFERENCES activite(id_activite)
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Table reservation_reunion
CREATE TABLE IF NOT EXISTS reservation_reunion (

id_reservation CHAR(7) PRIMARY KEY,
objet VARCHAR(100),
FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation)
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Table reservation_formation
CREATE TABLE IF NOT EXISTS reservation_formation (
id_reservation CHAR(7) PRIMARY KEY,
sujet VARCHAR(100),
nom_formateur VARCHAR(50),
prenom_formateur VARCHAR(50),
num_tel_formateur CHAR(10),
FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation)
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Table reservation_entretien
CREATE TABLE IF NOT EXISTS reservation_entretien (
id_reservation CHAR(7) PRIMARY KEY,
nature VARCHAR(100),
FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation)
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Table reservation_autre
CREATE TABLE IF NOT EXISTS reservation_autre (
id_reservation CHAR(7) PRIMARY KEY,
description VARCHAR(100),
FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation)
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Table reservation_pret_louer
CREATE TABLE IF NOT EXISTS reservation_pret_louer (
id_reservation CHAR(7) PRIMARY KEY,
nom_organisme VARCHAR(50),
nom_interlocuteur VARCHAR(50),
prenom_interlocuteur VARCHAR(50),
num_tel_interlocuteur CHAR(10),
type_activite VARCHAR(100),
FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation)
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Insérer des logins admin et user 
INSERT INTO type_utilisateur (nom_type) VALUES 
('admin'),
('user');

-- Insertion des données dans la table employe
INSERT INTO employe (id_employe, nom, prenom, telephone) VALUES
('A999999', 'admin', 'admin', '0000'),
('A888888', 'user', 'user', '0000'),
('E000001', 'Dupont', 'Pierre', '2614'),
('E000002', 'Lexpert', 'Noemie', '2614'),
('E000003', 'Dujardin', 'Océane', '2633'),
('E000004', 'Durand', 'Bill', '2696'),
('E000005', 'Dupont', 'Max', ''),
('E000006', 'Martin', 'Martin', '2678'),
('E000007', 'Legrand', 'Jean-Pierre', '2689'),
('E000008', 'Deneuve', 'Zoé', '2626');

-- Insertion des données dans la table login
INSERT INTO login (login, mdp, id_type, id_employe) VALUES
('admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'A999999'), -- mdp : admin
('user', '12dea96fec20593566ab75692c9949596833adc9', 2, 'A888888'),  -- mdp : user

-- mdp pour tous les utilisateurs en dessous : hashed_password 
('dupont.pierre', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000001'),
('lexpert.noemie', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000002'),
('dujardin.oceane', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000003'),
('durand.bill', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000004'),
('dupont.max', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000005'),
('martin.martin', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000006'),
('legrand.jeanpierre', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000007'),
('deneuve.zoe', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000008');

-- Insertion de nouveaux employés sans réservation avec leurs login (pour des tests
-- rapides).
INSERT INTO employe (id_employe, nom, prenom, telephone) VALUES 
('E000009', 'Morel', 'Laura', '0269500009'), 
('E000010', 'Benoit', 'Samuel', '0269600010'), 
('E000011', 'Fontaine', 'Claire', '0269700011'), 
('E000012', 'Rousseau', 'Julien', '0269800012'), 
('E000013', 'Giraud', 'Alice', '0269900013');

INSERT INTO login (login, mdp, id_type, id_employe) VALUES
-- mdp pour tous les utilisateurs en dessous : hashed_password 
('morel.laura', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000009'),
('benoit.samuel', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000010'),
('fontaine.claire', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000011'),
('rousseau.julien', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000012'),
('giraud.alice', 'f409ce90a1cd144912d1df8620215b2dc9fda731', 2, 'E000013');

-- Insertion des données dans la table salle
INSERT INTO salle (id_salle, nom, capacite, videoproj, ecran_xxl, ordinateur, type, logiciels, imprimante) VALUES 
('00000001', 'A6', 15, 1, 0, 4, 'PC portable', 'bureautique', 1), 
('00000002', 'Salle bleue', 18, 1, 1, 0, NULL, NULL, 0), 
('00000003', 'Salle ronde', 14, 1, 0, 0, NULL, NULL, 0), 
('00000004', 'Salle Picasso', 15, 0, 0, 0, NULL, NULL, 0), 
('00000005', 'Petite salle', 7, 1, 1, 0, NULL, NULL, 0), 
('00000006', 'A7', 4, 0, 0, 0, NULL, NULL, 0), 
('00000007', 'Salle patio', 6, 0, 0, 0, NULL, NULL, 0), 
('00000008', 'Salle Sydney', 20, 1, 0, 16, 'PC Windows', 'bureautique, java, Intellij', 0), 
('00000009', 'Salle Brisbane', 22, 1, 0, 18, 'PC Windows', 'bureautique, java, Intellij, photoshop', 1);

-- Insertion des données dans la table activite
INSERT INTO activite (id_activite, nom_activite) VALUES 
('A0000001', 'Réunion'), 
('A0000002', 'Formation'), 
('A0000003', 'Entretien de la salle'), 
('A0000004', 'Prêt'), 
('A0000005', 'Location'), 
('A0000006', 'Autre');

-- Insertion dans la table reservation
INSERT INTO reservation (id_reservation, id_salle, id_employe, id_activite, date_reservation, heure_debut, heure_fin) VALUES 
('R000001', '00000001', 'E000001', 'A0000004', '2024-10-07', '17:00:00', '19:00:00'),
('R000002', '00000004', 'E000001', 'A0000001', '2024-10-07', '15:00:00', '18:00:00'),
('R000003', '00000004', 'E000005', 'A0000001', '2024-10-07', '10:00:00', '11:00:00'),
('R000004', '00000004', 'E000002', 'A0000001', '2024-10-08', '09:00:00', '11:00:00'),
('R000005', '00000001', 'E000003', 'A0000004', '2024-10-08', '17:00:00', '19:00:00'),
('R000006', '00000008', 'E000007', 'A0000006', '2024-10-09', '09:00:00', '12:00:00'),
('R000007', '00000003', 'E000007', 'A0000001', '2024-10-07', '15:00:00', '18:00:00'),
('R000008', '00000001', 'E000003', 'A0000002', '2024-10-10', '08:00:00', '18:00:00'),
('R000009', '00000001', 'E000003', 'A0000002', '2024-10-11', '08:00:00', '18:00:00'),
('R000010', '00000003', 'E000008', 'A0000001', '2024-10-07', '10:00:00', '12:00:00'),
('R000011', '00000008', 'E000001', 'A0000006', '2024-10-10', '09:00:00', '12:00:00'),
('R000012', '00000006', 'E000007', 'A0000001', '2024-10-15', '09:00:00', '10:00:00'),
('R000013', '00000008', 'E000003', 'A0000003', '2024-10-11', '08:00:00', '17:00:00'),
('R000014', '00000009', 'E000007', 'A0000003', '2024-10-11', '08:00:00', '17:00:00'),
('R000015', '00000006', 'E000007', 'A0000001', '2024-10-16', '10:00:00', '11:00:00'),
('R000016', '00000006', 'E000005', 'A0000001', '2024-10-17', '14:00:00', '15:30:00'),
('R000017', '00000001', 'E000008', 'A0000005', '2024-10-18', '08:00:00', '13:00:00'),
('R000018', '00000001', 'E000005', 'A0000005', '2024-10-18', '13:00:00', '19:00:00');


-- Insertion dans la table reservation_pret_louer
INSERT INTO reservation_pret_louer (id_reservation, nom_organisme, nom_interlocuteur, prenom_interlocuteur, num_tel_interlocuteur, type_activite) VALUES 
('R000001', 'Club Gym', 'Legendre', 'Noémie', '0600000000', 'Réunion'),
('R000005', 'Club Gym', 'Legendre', 'Noémie', '0600000000', 'Assemblée Générale'),
('R000017', 'Mairie', 'Marin', 'Hector', '0666666666', 'Réunion'),
('R000018', 'Département', 'Tournefeuille', 'Michel', '0655555555', 'Réunion');

-- Insertion dans la table reservation_reunion
INSERT INTO reservation_reunion (id_reservation, objet) VALUES 
('R000002', 'Réunion avec client'),
('R000003', 'Préparation réunion client'),
('R000004', NULL),
('R000007', 'Présentation maquette'),
('R000010', 'Accueil nouveau membre'),
('R000012', 'Point avec stagiaire'),
('R000015', 'Visite tuteur IUT'),
('R000016', 'Validation maquette');

-- Insertion dans la table reservation_formation
INSERT INTO reservation_formation (id_reservation, sujet, nom_formateur, prenom_formateur, num_tel_formateur) VALUES 
('R000008', 'Bureautique', 'Leroux', 'Jacques', '0600000001'),
('R000009', 'Bureautique', 'Leroux', 'Jacques', '0600000001');

-- Insertion dans la table reservation_entretien
INSERT INTO reservation_entretien (id_reservation, nature) VALUES 
('R000013', 'Mise à jour logiciels'),
('R000014', 'Mise à jour logiciels');

-- Insertion dans la table reservation_autre
INSERT INTO reservation_autre (id_reservation, description) VALUES 
('R000006', 'Tests candidats'),
('R000011', 'Tests candidat');


-- Insertion des 200 réservations
INSERT INTO reservation (id_reservation, id_salle, id_employe, id_activite, date_reservation, heure_debut, heure_fin) VALUES
('R000023', '00000005', 'E000013', 'A0000005', '2025-01-02', '09:00:00', '11:00:00'),
('R000024', '00000006', 'E000009', 'A0000006', '2025-01-02', '11:00:00', '13:00:00'),
('R000025', '00000007', 'E000010', 'A0000001', '2025-01-02', '14:00:00', '16:00:00'),
('R000026', '00000008', 'E000011', 'A0000002', '2025-01-02', '16:00:00', '18:00:00'),
('R000027', '00000009', 'E000012', 'A0000003', '2025-01-03', '09:00:00', '11:00:00'),
('R000028', '00000001', 'E000013', 'A0000004', '2025-01-03', '11:00:00', '13:00:00'),
('R000029', '00000002', 'E000009', 'A0000005', '2025-01-03', '14:00:00', '16:00:00'),
('R000030', '00000003', 'E000010', 'A0000006', '2025-01-03', '16:00:00', '18:00:00'),
('R000031', '00000004', 'E000011', 'A0000001', '2025-01-04', '09:00:00', '11:00:00'),
('R000032', '00000005', 'E000012', 'A0000002', '2025-01-04', '11:00:00', '13:00:00'),
('R000033', '00000006', 'E000013', 'A0000003', '2025-01-04', '14:00:00', '16:00:00'),
('R000034', '00000007', 'E000009', 'A0000004', '2025-01-04', '16:00:00', '18:00:00'),
('R000035', '00000008', 'E000010', 'A0000005', '2025-01-05', '09:00:00', '11:00:00'),
('R000036', '00000009', 'E000011', 'A0000006', '2025-01-05', '11:00:00', '13:00:00'),
('R000037', '00000001', 'E000012', 'A0000001', '2025-01-05', '14:00:00', '16:00:00'),
('R000038', '00000002', 'E000013', 'A0000002', '2025-01-05', '16:00:00', '18:00:00'),
('R000039', '00000003', 'E000009', 'A0000003', '2025-01-06', '09:00:00', '11:00:00'),
('R000040', '00000004', 'E000010', 'A0000004', '2025-01-06', '11:00:00', '13:00:00'),
('R000041', '00000005', 'E000011', 'A0000005', '2025-01-06', '14:00:00', '16:00:00'),
('R000042', '00000006', 'E000012', 'A0000006', '2025-01-06', '16:00:00', '18:00:00'),
('R000043', '00000007', 'E000013', 'A0000001', '2025-01-07', '09:00:00', '11:00:00'),
('R000044', '00000008', 'E000009', 'A0000002', '2025-01-07', '11:00:00', '13:00:00'),
('R000045', '00000009', 'E000010', 'A0000003', '2025-01-07', '14:00:00', '16:00:00'),
('R000046', '00000001', 'E000011', 'A0000004', '2025-01-07', '16:00:00', '18:00:00'),
('R000047', '00000002', 'E000012', 'A0000005', '2025-01-08', '09:00:00', '11:00:00'),
('R000048', '00000003', 'E000013', 'A0000006', '2025-01-08', '11:00:00', '13:00:00'),
('R000049', '00000004', 'E000009', 'A0000001', '2025-01-08', '14:00:00', '16:00:00'),
('R000050', '00000005', 'E000010', 'A0000002', '2025-01-08', '16:00:00', '18:00:00'),
('R000051', '00000006', 'E000011', 'A0000003', '2025-01-09', '09:00:00', '11:00:00'),
('R000052', '00000007', 'E000012', 'A0000004', '2025-01-09', '11:00:00', '13:00:00'),
('R000053', '00000008', 'E000013', 'A0000005', '2025-01-09', '14:00:00', '16:00:00'),
('R000054', '00000009', 'E000009', 'A0000006', '2025-01-09', '16:00:00', '18:00:00'),
('R000055', '00000001', 'E000010', 'A0000001', '2025-01-10', '09:00:00', '11:00:00'),
('R000056', '00000002', 'E000011', 'A0000002', '2025-01-10', '11:00:00', '13:00:00'),
('R000057', '00000003', 'E000012', 'A0000003', '2025-01-10', '14:00:00', '16:00:00'),
('R000058', '00000004', 'E000013', 'A0000004', '2025-01-10', '16:00:00', '18:00:00'),
('R000059', '00000005', 'E000009', 'A0000005', '2025-01-11', '09:00:00', '11:00:00'),
('R000060', '00000006', 'E000010', 'A0000006', '2025-01-11', '11:00:00', '13:00:00'),
('R000061', '00000007', 'E000011', 'A0000001', '2025-01-11', '14:00:00', '16:00:00'),
('R000062', '00000008', 'E000012', 'A0000002', '2025-01-11', '16:00:00', '18:00:00'),
('R000063', '00000009', 'E000013', 'A0000003', '2025-01-12', '09:00:00', '11:00:00'),
('R000064', '00000001', 'E000009', 'A0000004', '2025-01-12', '11:00:00', '13:00:00'),
('R000065', '00000002', 'E000010', 'A0000005', '2025-01-12', '14:00:00', '16:00:00'),
('R000066', '00000003', 'E000011', 'A0000006', '2025-01-12', '16:00:00', '18:00:00'),
('R000067', '00000004', 'E000012', 'A0000001', '2025-01-13', '09:00:00', '11:00:00'),
('R000068', '00000005', 'E000013', 'A0000002', '2025-01-13', '11:00:00', '13:00:00'),
('R000069', '00000006', 'E000009', 'A0000003', '2025-01-13', '14:00:00', '16:00:00'),
('R000070', '00000007', 'E000010', 'A0000004', '2025-01-13', '16:00:00', '18:00:00'),
('R000071', '00000008', 'E000011', 'A0000005', '2025-01-14', '09:00:00', '11:00:00'),
('R000072', '00000009', 'E000012', 'A0000006', '2025-01-14', '11:00:00', '13:00:00'),
('R000073', '00000001', 'E000013', 'A0000001', '2025-01-14', '14:00:00', '16:00:00'),
('R000074', '00000002', 'E000009', 'A0000002', '2025-01-14', '16:00:00', '18:00:00'),
('R000075', '00000003', 'E000010', 'A0000003', '2025-01-15', '09:00:00', '11:00:00'),
('R000076', '00000004', 'E000011', 'A0000004', '2025-01-15', '11:00:00', '13:00:00'),
('R000077', '00000005', 'E000012', 'A0000005', '2025-01-15', '14:00:00', '16:00:00'),
('R000078', '00000006', 'E000013', 'A0000006', '2025-01-15', '16:00:00', '18:00:00'),
('R000079', '00000007', 'E000009', 'A0000001', '2025-01-16', '09:00:00', '11:00:00'),
('R000080', '00000008', 'E000010', 'A0000002', '2025-01-16', '11:00:00', '13:00:00'),
('R000081', '00000009', 'E000011', 'A0000003', '2025-01-16', '14:00:00', '16:00:00'),
('R000082', '00000001', 'E000012', 'A0000004', '2025-01-16', '16:00:00', '18:00:00'),
('R000083', '00000002', 'E000013', 'A0000005', '2025-01-17', '09:00:00', '11:00:00'),
('R000084', '00000003', 'E000009', 'A0000006', '2025-01-17', '11:00:00', '13:00:00'),
('R000085', '00000004', 'E000010', 'A0000001', '2025-01-17', '14:00:00', '16:00:00'),
('R000086', '00000005', 'E000011', 'A0000002', '2025-01-17', '16:00:00', '18:00:00'),
('R000087', '00000006', 'E000012', 'A0000003', '2025-01-18', '09:00:00', '11:00:00'),
('R000088', '00000007', 'E000013', 'A0000004', '2025-01-18', '11:00:00', '13:00:00'),
('R000089', '00000008', 'E000009', 'A0000005', '2025-01-18', '14:00:00', '16:00:00'),
('R000090', '00000009', 'E000010', 'A0000006', '2025-01-18', '16:00:00', '18:00:00'),
('R000091', '00000001', 'E000011', 'A0000001', '2025-01-19', '09:00:00', '11:00:00'),
('R000092', '00000002', 'E000012', 'A0000002', '2025-01-19', '11:00:00', '13:00:00'),
('R000093', '00000003', 'E000013', 'A0000003', '2025-01-19', '14:00:00', '16:00:00'),
('R000094', '00000004', 'E000009', 'A0000004', '2025-01-19', '16:00:00', '18:00:00'),
('R000095', '00000005', 'E000010', 'A0000005', '2025-01-20', '09:00:00', '11:00:00'),
('R000096', '00000006', 'E000011', 'A0000006', '2025-01-20', '11:00:00', '13:00:00'),
('R000097', '00000007', 'E000012', 'A0000001', '2025-01-20', '14:00:00', '16:00:00'),
('R000098', '00000008', 'E000013', 'A0000002', '2025-01-20', '16:00:00', '18:00:00'),
('R000099', '00000009', 'E000009', 'A0000003', '2025-01-21', '09:00:00', '11:00:00'),
('R000100', '00000001', 'E000010', 'A0000004', '2025-01-21', '11:00:00', '13:00:00');

-- Insertion des réservations
INSERT INTO reservation (id_reservation, id_salle, id_employe, id_activite, date_reservation, heure_debut, heure_fin) VALUES
('R000101', '00000002', 'E000011', 'A0000005', '2025-01-21', '14:00:00', '16:00:00'),
('R000102', '00000003', 'E000012', 'A0000006', '2025-01-21', '16:00:00', '18:00:00'),
('R000103', '00000004', 'E000013', 'A0000001', '2025-01-22', '09:00:00', '11:00:00'),
('R000104', '00000005', 'E000009', 'A0000002', '2025-01-22', '11:00:00', '13:00:00'),
('R000105', '00000006', 'E000010', 'A0000003', '2025-01-22', '14:00:00', '16:00:00'),
('R000106', '00000007', 'E000011', 'A0000004', '2025-01-22', '16:00:00', '18:00:00'),
('R000107', '00000008', 'E000012', 'A0000005', '2025-01-23', '09:00:00', '11:00:00'),
('R000108', '00000009', 'E000013', 'A0000006', '2025-01-23', '11:00:00', '13:00:00'),
('R000109', '00000001', 'E000009', 'A0000001', '2025-01-23', '14:00:00', '16:00:00'),
('R000110', '00000002', 'E000010', 'A0000002', '2025-01-23', '16:00:00', '18:00:00'),
('R000111', '00000003', 'E000011', 'A0000003', '2025-01-24', '09:00:00', '11:00:00'),
('R000112', '00000004', 'E000012', 'A0000004', '2025-01-24', '11:00:00', '13:00:00'),
('R000113', '00000005', 'E000013', 'A0000005', '2025-01-24', '14:00:00', '16:00:00'),
('R000114', '00000006', 'E000009', 'A0000006', '2025-01-24', '16:00:00', '18:00:00'),
('R000115', '00000007', 'E000010', 'A0000001', '2025-01-25', '09:00:00', '11:00:00'),
('R000116', '00000008', 'E000011', 'A0000002', '2025-01-25', '11:00:00', '13:00:00'),
('R000117', '00000009', 'E000012', 'A0000003', '2025-01-25', '14:00:00', '16:00:00'),
('R000118', '00000001', 'E000013', 'A0000004', '2025-01-25', '16:00:00', '18:00:00'),
('R000119', '00000002', 'E000009', 'A0000005', '2025-01-26', '09:00:00', '11:00:00'),
('R000120', '00000003', 'E000010', 'A0000006', '2025-01-26', '11:00:00', '13:00:00'),
('R000121', '00000004', 'E000011', 'A0000001', '2025-01-26', '14:00:00', '16:00:00'),
('R000122', '00000005', 'E000012', 'A0000002', '2025-01-26', '16:00:00', '18:00:00'),
('R000123', '00000006', 'E000013', 'A0000003', '2025-01-27', '09:00:00', '11:00:00'),
('R000124', '00000007', 'E000009', 'A0000004', '2025-01-27', '11:00:00', '13:00:00'),
('R000125', '00000008', 'E000010', 'A0000005', '2025-01-27', '14:00:00', '16:00:00'),
('R000126', '00000009', 'E000011', 'A0000006', '2025-01-27', '16:00:00', '18:00:00'),
('R000127', '00000001', 'E000012', 'A0000001', '2025-01-28', '09:00:00', '11:00:00'),
('R000128', '00000002', 'E000013', 'A0000002', '2025-01-28', '11:00:00', '13:00:00'),
('R000129', '00000003', 'E000009', 'A0000003', '2025-01-28', '14:00:00', '16:00:00'),
('R000130', '00000004', 'E000010', 'A0000004', '2025-01-28', '16:00:00', '18:00:00'),
('R000131', '00000005', 'E000011', 'A0000005', '2025-01-29', '09:00:00', '11:00:00'),
('R000132', '00000006', 'E000012', 'A0000006', '2025-01-29', '11:00:00', '13:00:00'),
('R000133', '00000007', 'E000013', 'A0000001', '2025-01-29', '14:00:00', '16:00:00'),
('R000134', '00000008', 'E000009', 'A0000002', '2025-01-29', '16:00:00', '18:00:00'),
('R000135', '00000009', 'E000010', 'A0000003', '2025-01-30', '09:00:00', '11:00:00'),
('R000136', '00000001', 'E000011', 'A0000004', '2025-01-30', '11:00:00', '13:00:00'),
('R000137', '00000002', 'E000012', 'A0000005', '2025-01-30', '14:00:00', '16:00:00'),
('R000138', '00000003', 'E000013', 'A0000006', '2025-01-30', '16:00:00', '18:00:00'),
('R000139', '00000004', 'E000009', 'A0000001', '2025-01-31', '09:00:00', '11:00:00'),
('R000140', '00000005', 'E000010', 'A0000002', '2025-01-31', '11:00:00', '13:00:00'),
('R000141', '00000006', 'E000011', 'A0000003', '2025-01-31', '14:00:00', '16:00:00'),
('R000142', '00000007', 'E000012', 'A0000004', '2025-01-31', '16:00:00', '18:00:00'),
('R000143', '00000008', 'E000013', 'A0000005', '2025-02-01', '09:00:00', '11:00:00'),
('R000144', '00000009', 'E000009', 'A0000006', '2025-02-01', '11:00:00', '13:00:00');

-- Insertion des réservations
INSERT INTO reservation (id_reservation, id_salle, id_employe, id_activite, date_reservation, heure_debut, heure_fin) VALUES
('R000145', '00000006', 'E000010', 'A0000001', '2025-01-25', '09:00:00', '11:00:00'),
('R000146', '00000007', 'E000011', 'A0000002', '2025-01-25', '11:00:00', '13:00:00'),
('R000147', '00000008', 'E000012', 'A0000003', '2025-01-25', '14:00:00', '16:00:00'),
('R000148', '00000009', 'E000013', 'A0000004', '2025-01-25', '16:00:00', '18:00:00'),
('R000149', '00000001', 'E000009', 'A0000005', '2025-01-26', '09:00:00', '11:00:00'),
('R000150', '00000002', 'E000010', 'A0000006', '2025-01-26', '11:00:00', '13:00:00'),
('R000151', '00000003', 'E000011', 'A0000001', '2025-01-26', '14:00:00', '16:00:00'),
('R000152', '00000004', 'E000012', 'A0000002', '2025-01-26', '16:00:00', '18:00:00'),
('R000153', '00000005', 'E000013', 'A0000003', '2025-01-27', '09:00:00', '11:00:00'),
('R000154', '00000006', 'E000009', 'A0000004', '2025-01-27', '11:00:00', '13:00:00'),
('R000155', '00000007', 'E000010', 'A0000005', '2025-01-27', '14:00:00', '16:00:00'),
('R000156', '00000008', 'E000011', 'A0000006', '2025-01-27', '16:00:00', '18:00:00'),
('R000157', '00000009', 'E000012', 'A0000001', '2025-01-28', '09:00:00', '11:00:00'),
('R000158', '00000001', 'E000013', 'A0000002', '2025-01-28', '11:00:00', '13:00:00'),
('R000159', '00000002', 'E000009', 'A0000003', '2025-01-28', '14:00:00', '16:00:00'),
('R000160', '00000003', 'E000010', 'A0000004', '2025-01-28', '16:00:00', '18:00:00'),
('R000161', '00000004', 'E000011', 'A0000005', '2025-01-29', '09:00:00', '11:00:00'),
('R000162', '00000005', 'E000012', 'A0000006', '2025-01-29', '11:00:00', '13:00:00'),
('R000163', '00000006', 'E000013', 'A0000001', '2025-01-29', '14:00:00', '16:00:00'),
('R000164', '00000007', 'E000009', 'A0000002', '2025-01-29', '16:00:00', '18:00:00'),
('R000165', '00000008', 'E000010', 'A0000003', '2025-01-30', '09:00:00', '11:00:00'),
('R000166', '00000009', 'E000011', 'A0000004', '2025-01-30', '11:00:00', '13:00:00'),
('R000167', '00000001', 'E000012', 'A0000005', '2025-01-30', '14:00:00', '16:00:00'),
('R000168', '00000002', 'E000013', 'A0000006', '2025-01-30', '16:00:00', '18:00:00'),
('R000169', '00000003', 'E000009', 'A0000001', '2025-01-31', '09:00:00', '11:00:00'),
('R000170', '00000004', 'E000010', 'A0000002', '2025-01-31', '11:00:00', '13:00:00'),
('R000171', '00000005', 'E000011', 'A0000003', '2025-01-31', '14:00:00', '16:00:00'),
('R000172', '00000006', 'E000012', 'A0000004', '2025-01-31', '16:00:00', '18:00:00');
CREATE TABLE IF NOT EXISTS gravite (
    id_gravite INT AUTO_INCREMENT PRIMARY KEY,
    intitule VARCHAR(255) NOT NULL
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO gravite (intitule) VALUES
('salle inutilisable'),
('incident très gênant'),
('peu gênant'),
('incident sans aucun impact sur l''usage de la salle');

CREATE TABLE IF NOT EXISTS incident (
    id_incident INT AUTO_INCREMENT PRIMARY KEY,
    resume VARCHAR(50) NOT NULL,
    description VARCHAR(200),
    service_technique BOOLEAN,
    id_gravite INT NOT NULL,
    id_reservation CHAR(7) NOT NULL,
    id_employe CHAR(7) NOT NULL,
    date_signalement DATE NOT NULL,
    heure_signalement TIME NOT NULL,
    FOREIGN KEY (id_gravite) REFERENCES gravite(id_gravite),
    FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Insertion incident
INSERT INTO incident (resume, description, service_technique, id_gravite, id_reservation, id_employe, date_signalement, heure_signalement) VALUES
('Fuite d\'eau', 'Fuite d\'eau au plafond, risque d\'inondation.', TRUE, 1, 'R000001', 'E000001', '2025-03-01', '08:30:00'),
('Problème électricité', 'Panne de courant dans toute la salle.', TRUE, 1, 'R000002', 'E000001', '2025-03-02', '10:15:00'),
('Climatisation en panne', 'La climatisation ne fonctionne plus.', FALSE, 2, 'R000003', 'E000001', '2025-03-03', '14:00:00'),
('Vidéo-projecteur HS', 'Le projecteur ne s\'allume pas.', FALSE, 3, 'R000004', 'E000001', '2025-03-04', '09:45:00'),
('Chaise cassée', 'Une chaise est cassée, danger potentiel.', FALSE, 3, 'R000005', 'E000001', '2025-03-05', '11:30:00'),
('Porte bloquée', 'La porte de la salle ne s\'ouvre plus.', TRUE, 2, 'R000006', 'E000002', '2025-03-06', '16:20:00'),
('Table bancale', 'Une table est instable et branlante.', FALSE, 4, 'R000007', 'E000002', '2025-03-07', '13:10:00'),
('Tâches sur le mur', 'Des tâches de peinture sont visibles.', FALSE, 4, 'R000008', 'E000002', '2025-03-08', '15:40:00'),
('WiFi hors service', 'Impossible de se connecter au réseau.', TRUE, 2, 'R000009', 'E000002', '2025-03-09', '08:00:00'),
('Fenêtre cassée', 'Une vitre est brisée, risque de blessure.', TRUE, 1, 'R000010', 'E000002', '2025-03-10', '17:25:00');
