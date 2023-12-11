CREATE DATABASE IF NOT EXISTS dolphin_crm;
USE dolphin_crm;

CREATE TABLE IF NOT EXISTS Users (
    id INT NOT NULL AUTO_INCREMENT,
    `firstname` VARCHAR(50) NOT NULL,
    `lastname` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `role` VARCHAR(50),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4;

INSERT INTO `Users` (`firstname`, `lastname`, `password`, `email`, `role`, `created_at`)
VALUES ('Jemoi', 'Johnson', SHA2('password123', 512), 'admin@project2.com', 'Admin', CURRENT_TIMESTAMP);

CREATE TABLE IF NOT EXISTS `Contacts` (
    id INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(10) NOT NULL,
    `firstname` VARCHAR(50) NOT NULL,
    `lastname` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `telephone` VARCHAR(15) NOT NULL,
    `company` VARCHAR(100) NOT NULL,
    `type` VARCHAR(15) NOT NULL,
    `assigned_to` INT(10) NOT NULL,
    `created_by` INT(10) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`assigned_to`) REFERENCES `Users`(`id`),
    FOREIGN KEY (`created_by`) REFERENCES `Users`(`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2000 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Notes` (
    id INT NOT NULL AUTO_INCREMENT,
    `contact_id` INT(10) NOT NULL,
    `comment` TEXT,
    `created_by` INT(10) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`contact_id`) REFERENCES `Contacts`(`id`),
    FOREIGN KEY (`created_by`) REFERENCES `Users`(`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3000 DEFAULT CHARSET=utf8mb4;
