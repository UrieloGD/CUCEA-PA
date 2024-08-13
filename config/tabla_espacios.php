<?php

// Crear la tabla Espacios
$sql_create = "CREATE TABLE IF NOT EXISTS Espacios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Edificio VARCHAR(10),
    Espacio VARCHAR(20),
    Etiqueta VARCHAR(50)
);";

if (mysqli_query($conn, $sql_create)) {
    echo "<br>Tabla Espacios creada exitosamente";
} else {
    echo "<br>Error creando tabla Espacios: " . mysqli_error($conn);
}

// Insertar datos en la tabla Espacios
$espacios = "INSERT INTO Espacios (Edificio, Espacio, Etiqueta) VALUES

-- Edificio A

('CEDA', 'A-101', 'Oficina Administrativa'),
('CEDA', 'A-102', 'Oficina Administrativa'),
('CEDA', 'A-103', 'Oficina Administrativa'),
('CEDA', 'A-104', 'Oficina Administrativa'),
('CEDA', 'A-201', 'Oficina Administrativa'),
('CEDA', 'A-202', 'Bodega'),
('CEDA', 'A-203', 'Aula'),
('CEDA', 'A-204', 'Oficina Administrativa'),
('CEDA', 'A-205', 'Oficina Administrativa'),
('CEDA', 'A-206', 'Oficina Administrativa'),
('CEDA', 'A-301', 'Aula'),
('CEDA', 'A-302', 'Aula'),
('CEDA', 'A-303', 'Aula'),
('CEDA', 'A-304', 'Aula'),
('CEDA', 'A-305', 'Bodega'),
('CEDA', 'A-306', 'Aula'),
('CEDA', 'A-307', 'Aula'),
('CEDA', 'A-308', 'Aula'),
('CEDA', 'A-309', 'Oficina Administrativa'),

-- Edificio B

('CEDB', 'B-101', 'Bodega'),
('CEDB', 'B-102', 'Oficina Administrativa'),
('CEDB', 'B-103', 'Oficina Administrativa'),
('CEDB', 'B-104', 'Oficina Administrativa'),
('CEDB', 'B-105', 'Oficina Administrativa'),
('CEDB', 'B-106', 'Oficina Administrativa'),
('CEDB', 'B-107', 'Oficina Administrativa'),
('CEDB', 'B-108', 'Oficina Administrativa'),
('CEDB', 'B-109', 'Oficina Administrativa'),
('CEDB', 'B-110', 'Oficina Administrativa'),
('CEDB', 'B-107', 'Bodega'),
('CEDB', 'B-108/B108-A', 'Aula'),
('CEDB', 'B-109', 'Auditorio'),
('CEDB', 'B-110', 'Oficina Administrativa'),
('CEDB', 'B-201', 'Oficina Administrativa'),
('CEDB', 'B-202', 'Oficina Administrativa'),
('CEDB', 'B-301', 'Aula'),
('CEDB', 'B-302', 'Aula'),
('CEDB', 'B-303', 'Aula'),
('CEDB', 'B-304', 'Aula'),
('CEDB', 'B-305', 'Aula'),
('CEDB', 'B-306', 'Oficina Administrativa'),
('CEDB', 'B-307', 'Oficina Administrativa'),
('CEDB', 'B-308', 'Oficina Administrativa'),
('CEDB', 'Bodega', 'Bodega'),

-- Edificio C

('CEDC', 'C-101', 'Aula'),
('CEDC', 'C-102', 'Aula'),
('CEDC', 'C-103', 'Aula'),
('CEDC', 'C-104', 'Aula'),
('CEDC', 'C-105', 'Aula'),
('CEDC', 'Proulex Aula 1', 'Aula'),
('CEDC', 'Proulex Aula 2', 'Aula'),
('CEDC', 'Proulex Aula 3', 'Aula'),
('CEDC', 'Proulex Aula 4', 'Aula'),
('CEDC', 'Proulex Aula 5', 'Aula'),
('CEDC', 'Proulex Aula 6', 'Aula'),
('CEDC', 'Proulex Aula 7', 'Aula'),
('CEDC', 'Proulex Aula 8', 'Aula'),
('CEDC', 'Proulex Aula 9', 'Aula'),
('CEDC', 'C-201', 'Aula'),
('CEDC', 'C-202', 'Aula'),
('CEDC', 'C-203', 'Aula'),
('CEDC', 'C-204', 'Aula'),
('CEDC', 'C-205', 'Aula'),
('CEDC', 'C-206', 'Aula'),
('CEDC', 'C-207', 'Aula'),
('CEDC', 'C-208', 'Aula'),
('CEDC', 'C-209', 'Aula'),
('CEDC', 'C-301', 'Aula'),
('CEDC', 'C-302', 'Aula'),
('CEDC', 'C-303', 'Aula'),
('CEDC', 'C-304', 'Aula'),
('CEDC', 'C-305', 'Bodega'),
('CEDC', 'C-306', 'Bodega'),
('CEDC', 'C-307', 'Aula'),
('CEDC', 'C-308', 'Aula'),
('CEDC', 'C-309', 'Aula'),
('CEDC', 'C-310', 'Aula'),

-- Edificio D

('CEDD', 'D-101', 'Oficina Administrativa'),
('CEDD', 'D-102', 'Aula'),
('CEDD', 'D-103', 'Aula'),
('CEDD', 'D-105', 'Bodega'),
('CEDD', 'D-106', 'Aula'),
('CEDD', 'D-107', 'Aula'),
('CEDD', 'D-108', 'Aula'),
('CEDD', 'D-201', 'Aula'),
('CEDD', 'D-202', 'Aula'),
('CEDD', 'D-203', 'Aula'),
('CEDD', 'D-204', 'Aula'),
('CEDD', 'D-205', 'Oficina Administrativa'),
('CEDD', 'D-206', 'Aula'),
('CEDD', 'D-207', 'Aula'),
('CEDD', 'D-208', 'Aula'),
('CEDD', 'D-209', 'Aula'),
('CEDD', 'D-301', 'Aula'),
('CEDD', 'D-302', 'Aula'),
('CEDD', 'D-303', 'Aula'),
('CEDD', 'D-304', 'Aula'),
('CEDD', 'D-305', 'Aula'),
('CEDD', 'D-306', 'Aula'),
('CEDD', 'D-307', 'Aula'),
('CEDD', 'D-308', 'Aula'),
('CEDD', 'D-309', 'Aula'),
('CEDD', 'Bodega', 'Bodega'),

-- Edificio E

('CEDE', 'E-101', 'Oficina Administrativa'),
('CEDE', 'E-102', 'Oficina Administrativa'),
('CEDE', 'E-103', 'Aula'),
('CEDE', 'E-104', 'Aula'),
('CEDE', 'E-106', 'Aula'),
('CEDE', 'E-107', 'Aula'),
('CEDE', 'E-108', 'Aula'),
('CEDE', 'E-109', 'Aula'),
('CEDE', 'E-201', 'Aula'),
('CEDE', 'E-202', 'Aula'),
('CEDE', 'E-203', 'Aula'),
('CEDE', 'E-204', 'Aula'),
('CEDE', 'E-205', 'Aula'),
('CEDE', 'E-206', 'Oficina Administrativa'),
('CEDE', 'E-207', 'Aula'),
('CEDE', 'E-208', 'Aula'),
('CEDE', 'E-209', 'Aula'),
('CEDE', 'E-210', 'Aula'),
('CEDE', 'E-301', 'Aula'),
('CEDE', 'E-302', 'Aula'),
('CEDE', 'E-303', 'Aula'),
('CEDE', 'E-304', 'Aula'),
('CEDE', 'E-305', 'Aula'),
('CEDE', 'E-306', 'Aula'),
('CEDE', 'E-307', 'Aula'),
('CEDE', 'E-308', 'Aula'),
('CEDE', 'E-309', 'Aula'),

-- Edificio F

('CEDF', 'F-102', 'Oficina Administrativa'),
('CEDF', 'F-103', 'Oficina Administrativa'),
('CEDF', 'F-104', 'Oficina Administrativa'),
('CEDF', 'F-105', 'Aula'),
('CEDF', 'F-106', 'Aula'),
('CEDF', 'F-108', 'Laboratorio'),
('CEDF', 'F-201', 'Aula'),
('CEDF', 'F-202', 'Aula'),
('CEDF', 'F-203', 'Aula'),
('CEDF', 'F-204', 'Aula'),
('CEDF', 'F-205', 'Aula'),
('CEDF', 'F-206', 'Aula'),
('CEDF', 'F-207', 'Aula'),
('CEDF', 'F-208', 'Aula'),
('CEDF', 'F-209', 'Aula'),
('CEDF', 'F-301', 'Aula'),
('CEDF', 'F-302', 'Aula'),
('CEDF', 'F-303', 'Aula'),
('CEDF', 'F-304', 'Aula'),
('CEDF', 'F-305', 'Aula'),
('CEDF', 'F-306', 'Aula'),
('CEDF', 'F-307', 'Aula'),
('CEDF', 'F-308', 'Aula'),
('CEDF', 'F-309', 'Aula'),
('CEDF', 'Bodega', 'Bodega'),

-- Edificio G

('CEDG', 'G-101-A', 'Oficina Administrativa'),
('CEDG', 'G-101', 'Laboratorio'),
('CEDG', 'G-102', 'Bodega'),
('CEDG', 'G-103', 'Laboratorio'),
('CEDG', 'G-106', 'Bodega'),
('CEDG', 'G-107', 'Restaurante'),
('CEDG', 'G-108', 'Oficina Administrativa'),
('CEDG', 'G-109', 'Oficina Administrativa'),
('CEDG', 'G-201', 'Oficina Administrativa'),
('CEDG', 'G-202', 'Oficina Administrativa'),
('CEDG', 'G-203', 'Oficina Administrativa'),
('CEDG', 'G-206', 'Oficina Administrativa'),
('CEDG', 'G-207', 'Oficina Administrativa'),
('CEDG', 'G-208', 'Oficina Administrativa'),
('CEDG', 'G-301', 'Oficina Administrativa'),
('CEDG', 'G-302', 'Oficina Administrativa'),
('CEDG', 'G-303', 'Oficina Administrativa'),
('CEDG', 'G-306', 'Oficina Administrativa'),
('CEDG', 'G-307', 'Oficina Administrativa'),
('CEDG', 'G-309', 'Oficina Administrativa'),

-- Edificio H

('CEDH', 'H-101', 'Oficina Administrativa'),
('CEDH', 'H-102', 'Oficina Administrativa'),
('CEDH', 'H-103', 'Oficina Administrativa'),
('CEDH', 'H-104', 'Bodega'),
('CEDH', 'H-105', 'Oficina Administrativa'),
('CEDH', 'H-106', 'Oficina Administrativa'),
('CEDH', 'H-201', 'Bodega'),
('CEDH', 'H-202', 'Bodega'),
('CEDH', 'H-205', 'Bodega'),
('CEDH', 'H-301', 'Aula'),
('CEDH', 'H-302', 'Aula'),
('CEDH', 'H-303', 'Aula'),
('CEDH', 'H-304', 'Aula'),
('CEDH', 'H-305', 'Bodega'),
('CEDH', 'H-306', 'Aula'),
('CEDH', 'H-307', 'Aula'),
('CEDH', 'H-308', 'Aula'),
('CEDH', 'H-309', 'Aula'),
('CEDH', 'Bodega', 'Bodega'),

-- Edificio I

('CEDI', 'I-101', 'Bodega'),
('CEDI', 'I-102', 'Oficina Administrativa'),
('CEDI', 'I-103', 'Oficina Administrativa'),
('CEDI', 'I-104', 'Oficina Administrativa'),
('CEDI', 'I-105', 'Oficina Administrativa'),
('CEDI', 'I-106', 'Bodega'),
('CEDI', 'I-107', 'Laboratorio'),
('CEDI', 'I-108', 'Oficina Administrativa'),
('CEDI', 'I-110', 'Auditorio'),
('CEDI', 'I-201', 'Aula'),
('CEDI', 'I-202', 'Oficina Administrativa'),
('CEDI', 'I-203', 'Laboratorio'),
('CEDI', 'I-204', 'Laboratorio'),
('CEDI', 'I-205', 'Aula'),
('CEDI', 'I-206', 'Aula'),
('CEDI', 'I-207', 'Aula'),
('CEDI', 'I-208', 'Aula'),
('CEDI', 'I-301', 'Aula'),
('CEDI', 'I-302', 'Aula'),
('CEDI', 'I-303', 'Aula'),
('CEDI', 'I-304', 'Aula'),
('CEDI', 'I-305', 'Aula'),
('CEDI', 'I-306', 'Aula'),
('CEDI', 'I-307', 'Aula'),
('CEDI', 'I-308', 'Aula'),
('CEDI', 'I-309', 'Aula'),
('CEDI', 'Bodega', 'Bodega'),


-- Edificio J

('CEDJ', 'J-101', 'Oficina Administrativa'),
('CEDJ', 'J-102', 'Oficina Administrativa'),
('CEDJ', 'J-104', 'Bodega'),
('CEDJ', 'J-201', 'Aula'),
('CEDJ', 'J-202', 'Aula'),
('CEDJ', 'J-203', 'Aula'),
('CEDJ', 'J-204', 'Aula'),
('CEDJ', 'J-205', 'Aula'),
('CEDJ', 'J-206', 'Aula'),
('CEDJ', 'J-207', 'Aula'),
('CEDJ', 'J-208', 'Aula'),
('CEDJ', 'J-209', 'Aula'),
('CEDJ', 'J-301', 'Aula'),
('CEDJ', 'J-302', 'Aula'),
('CEDJ', 'J-303', 'Aula'),
('CEDJ', 'J-304', 'Aula'),
('CEDJ', 'J-305', 'Aula'),
('CEDJ', 'J-306', 'Aula'),
('CEDJ', 'J-307', 'Aula'),
('CEDJ', 'J-308', 'Aula'),
('CEDJ', 'J-309', 'Aula'),
('CEDJ', 'Bodega', 'Bodega'),

-- Edificio K

('CEDK-K-102', 'CEDK', 'Oficina Administrativa'),
('CEDK-K-103', 'CEDK', 'Bodega'),
('CEDK-K-109', 'CEDK', 'Bodega'),
('CEDK-K-110', 'CEDK', 'Oficina Administrativa'),
('CEDK-K-111', 'CEDK', 'Bodega'),
('CEDK-K-112', 'CEDK', 'Bodega'),
('CEDK-K-114', 'CEDK', 'Laboratorio'),
('CEDK-K-115', 'CEDK', 'Oficina Administrativa'),
('CEDK-K-202', 'CEDK', 'Oficina Administrativa'),
('CEDK-K-203', 'CEDK', 'Aula'),
('CEDK-K-204', 'CEDK', 'Aula'),
('CEDK-K-205', 'CEDK', 'Aula'),
('CEDK-K-206', 'CEDK', 'Aula'),
('CEDK-K-207', 'CEDK', 'Aula'),
('CEDK-K-208', 'CEDK', 'Aula'),
('CEDK-K-209', 'CEDK', 'Aula'),
('CEDK-K-210', 'CEDK', 'Aula'),
('CEDK-K-302', 'CEDK', 'Oficina Administrativa'),
('CEDK-K-303', 'CEDK', 'Oficina Administrativa'),
('CEDK-Bodega', 'CEDK', 'Bodega'),

-- Edificio L

('CEDL-L-101', 'CEDL', 'Laboratorio'),
('CEDL-L-102', 'CEDL', 'Laboratorio'),
('CEDL-L-103', 'CEDL', 'Laboratorio'),
('CEDL-L-104', 'CEDL', 'Laboratorio'),
('CEDL-L-105', 'CEDL', 'Oficina Administrativa'),
('CEDL-L-108', 'CEDL', 'Laboratorio'),
('CEDL-L-109', 'CEDL', 'Oficina Administrativa'),
('CEDL-L-201', 'CEDL', 'Laboratorio'),
('CEDL-L-202', 'CEDL', 'Laboratorio'),
('CEDL-L-204', 'CEDL', 'Laboratorio'),
('CEDL-L-205', 'CEDL', 'Oficina Administrativa'),
('CEDL-L-206', 'CEDL', 'Laboratorio'),
('CEDL-L-207', 'CEDL', 'Laboratorio'),
('CEDL-L-208', 'CEDL', 'Laboratorio'),
('CEDL-L-209', 'CEDL', 'Laboratorio'),
('CEDL-L-301', 'CEDL', 'Laboratorio'),
('CEDL-L-302', 'CEDL', 'Laboratorio'),
('CEDL-L-303', 'CEDL', 'Laboratorio'),
('CEDL-L-304', 'CEDL', 'Oficina Administrativa'),
('CEDL-L-305', 'CEDL', 'Oficina Administrativa'),
('CEDL-L-306', 'CEDL', 'Aula'),
('CEDL-L-308', 'CEDL', 'Oficina Administrativa'),
('CEDL-Bodega', 'CEDL', 'Bodega'),

-- Edificio M

('CEDM-M-101', 'CEDM', 'Laboratorio'),
('CEDM-M-102', 'CEDM', 'Aula'),
('CEDM-M-103', 'CEDM', 'Aula'),
('CEDM-M-104', 'CEDM', 'Aula'),
('CEDM-M-105', 'CEDM', 'Aula'),
('CEDM-M-106', 'CEDM', 'Aula'),
('CEDM-M-107', 'CEDM', 'Oficina Administrativa'),
('CEDM-M-108', 'CEDM', 'Oficina Administrativa'),
('CEDM-M-201', 'CEDM', 'Oficina Administrativa'),
('CEDM-M-203', 'CEDM', 'Oficina Administrativa'),
('CEDM-Bodega', 'CEDM', 'Bodega'),

-- Edificio N

('CEDN-N-101', 'CEDN', 'Laboratorio'),
('CEDN-N-102', 'CEDN', 'Laboratorio'),
('CEDN-N-103', 'CEDN', 'Aula'),
('CEDN-N-104', 'CEDN', 'Aula'),
('CEDN-N-105', 'CEDN', 'Aula'),
('CEDN-N-106', 'CEDN', 'Aula'),
('CEDN-N-107', 'CEDN', 'Aula'),
('CEDN-N-108', 'CEDN', 'Aula'),
('CEDN-N-200', 'CEDN', 'Oficina Administrativa'),
('CEDN-N-201', 'CEDN', 'Aula'),
('CEDN-N-202', 'CEDN', 'Aula'),
('CEDN-N-203', 'CEDN', 'Aula'),
('CEDN-N-204', 'CEDN', 'Aula'),
('CEDN-N-205', 'CEDN', 'Aula'),
('CEDN-N-206A', 'CEDN', 'Oficina Administrativa'),
('CEDN-N-206', 'CEDN', 'Laboratorio'),
('CEDN-N-207', 'CEDN', 'Aula'),
('CEDN-N-208', 'CEDN', 'Aula'),
('CEDN-N-209', 'CEDN', 'Aula'),
('CEDN-N-210', 'CEDN', 'Aula'),
('CEDN-N-211', 'CEDN', 'Aula'),
('CEDN-N-212', 'CEDN', 'Aula'),
('CEDN-N-213', 'CEDN', 'Aula'),
('CEDN-N-300', 'CEDN', 'Aula'),
('CEDN-N-301', 'CEDN', 'Aula'),
('CEDN-N-302', 'CEDN', 'Oficina Administrativa'),
('CEDN-N-304', 'CEDN', 'Oficina Administrativa'),
('CEDN-Bodega', 'CEDN', 'Bodega'),
('CEDN-N-305', 'CEDN', 'Aula'),
('CEDN-N-306', 'CEDN', 'Aula'),
('CEDN-N-307', 'CEDN', 'Aula'),
('CEDN-N-308', 'CEDN', 'Aula'),
('CEDN-N-309', 'CEDN', 'Aula'),
('CEDN-N-310', 'CEDN', 'Aula'),
('CEDN-N-311', 'CEDN', 'Aula'),

-- Aulas Amplias

('CEDAA-Aula-1', 'CEDAA', 'Aula Amplia'),
('CEDAA-Aula-2', 'CEDAA', 'Aula Amplia'),
('CEDAA-Aula-3', 'CEDAA', 'Aula Amplia'),
('CEDAA-Aula-4', 'CEDAA', 'Aula Amplia'),
('CEDAA-Aula-5', 'CEDAA', 'Aula Amplia'),
('CEDAA-Aula-6', 'CEDAA', 'Aula Amplia'),
('CEDAA-Bodega', 'CEDAA', 'Bodega')";


if (mysqli_query($conn, $espacios)) {
    echo "<br>Datos insertados exitosamente en la tabla Espacios";
} else {
    echo "<br>Error insertando Espacios: " . mysqli_error($conn);
}