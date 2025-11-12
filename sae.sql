SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS sae;
USE sae;

-- PROFESSORES
CREATE TABLE professores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(80) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  google_id VARCHAR(100) UNIQUE,
  foto TEXT,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ultimo_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- EQUIPAMENTOS
CREATE TABLE equipamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome_equip VARCHAR(40) NOT NULL,
  tipo ENUM('laboratorio','guardiao') NOT NULL,
  quantidade_total INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- AGENDAMENTOS (aula reservada)
CREATE TABLE agendamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  professor_id INT NOT NULL,
  data DATE NOT NULL,
  aula TINYINT UNSIGNED NOT NULL,
  periodo TINYINT UNSIGNED DEFAULT NULL, -- 1=Manhã, 2=Tarde, 3=Noite (se usar)
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  CONSTRAINT fk_agendamento_professor 
    FOREIGN KEY (professor_id) REFERENCES professores(id) 
    ON DELETE CASCADE,

  CONSTRAINT uk_prof_horario UNIQUE (professor_id, data, aula),

  INDEX idx_data_aula (data, aula)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- RESERVA DE EQUIPAMENTOS
CREATE TABLE reservas_equipamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  agendamento_id INT NOT NULL,
  equipamento_id INT NOT NULL,
  quantidade INT DEFAULT NULL, -- NULL = laboratório (uso total)

  CONSTRAINT fk_reserva_agendamento
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_reserva_equipamento
    FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id)
    ON DELETE CASCADE,

  INDEX idx_equipamento_agenda (equipamento_id, agendamento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
