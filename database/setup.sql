-- Database setup for Kaggle Project
-- This file will be automatically executed when the MySQL container starts

CREATE DATABASE IF NOT EXISTS kaggle_project;
USE kaggle_project;

-- Create table for UA Scores data (Urban Areas Quality of Life)
-- Using exact column names from CSV
CREATE TABLE IF NOT EXISTS uascores (
    ID INT,
    UA_Name VARCHAR(255),
    UA_Country VARCHAR(255),
    UA_Continent VARCHAR(100),
    Housing DECIMAL(10,6),
    `Cost of Living` DECIMAL(10,6),
    Startups DECIMAL(10,6),
    `Venture Capital` DECIMAL(10,6),
    `Travel Connectivity` DECIMAL(10,6),
    Commute DECIMAL(10,6),
    `Business Freedom` DECIMAL(10,6),
    Safety DECIMAL(10,6),
    Healthcare DECIMAL(10,6),
    Education DECIMAL(10,6),
    `Environmental Quality` DECIMAL(10,6),
    Economy DECIMAL(10,6),
    Taxation DECIMAL(10,6),
    `Internet Access` DECIMAL(10,6),
    `Leisure & Culture` DECIMAL(10,6),
    Tolerance DECIMAL(10,6),
    Outdoors DECIMAL(10,6),
    PRIMARY KEY (ID),
    INDEX idx_ua_name (UA_Name),
    INDEX idx_country (UA_Country),
    INDEX idx_continent (UA_Continent)
);

-- Create separate tables for different dataset sizes
CREATE TABLE IF NOT EXISTS uascores_1k LIKE uascores;
CREATE TABLE IF NOT EXISTS uascores_10k LIKE uascores;

-- Create table for performance metrics
CREATE TABLE IF NOT EXISTS performance_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    metric_name VARCHAR(100) NOT NULL,
    metric_value DECIMAL(15,4),
    metric_type VARCHAR(50),
    calculation_date DATE,
    additional_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_metric_name (metric_name),
    INDEX idx_metric_type (metric_type),
    INDEX idx_calc_date (calculation_date)
);

-- Create table for data import logs
CREATE TABLE IF NOT EXISTS import_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255),
    import_date DATETIME,
    records_imported INT,
    records_failed INT,
    import_status ENUM('success', 'partial', 'failed') DEFAULT 'success',
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CSV data will be loaded by PHP script after database initialization

-- Insert some sample data to verify table creation
INSERT INTO performance_metrics (metric_name, metric_value, metric_type, calculation_date) VALUES
('Average Housing Score', 0.00, 'statistical', CURDATE()),
('Total Urban Areas', 0, 'count', CURDATE()),
('Countries Covered', 0, 'count', CURDATE());

-- Create a view for UA scores statistics
CREATE OR REPLACE VIEW uascores_statistics AS
SELECT 
    COUNT(*) as total_cities,
    COUNT(DISTINCT UA_Country) as total_countries,
    COUNT(DISTINCT UA_Continent) as total_continents,
    AVG(Housing) as avg_housing_score,
    AVG(Safety) as avg_safety_score,
    AVG(Healthcare) as avg_healthcare_score,
    AVG(Education) as avg_education_score,
    MIN(Cost_of_Living) as min_cost_living,
    MAX(Cost_of_Living) as max_cost_living
FROM uascores;

-- Create a view for continent performance
CREATE OR REPLACE VIEW continent_performance AS
SELECT 
    UA_Continent,
    COUNT(*) as cities_count,
    AVG(Housing) as avg_housing,
    AVG(Safety) as avg_safety,
    AVG(Healthcare) as avg_healthcare,
    AVG(Education) as avg_education,
    AVG(Cost_of_Living) as avg_cost_living
FROM uascores
GROUP BY UA_Continent
ORDER BY avg_housing DESC;

-- Create the kaggle_user and grant privileges
CREATE USER IF NOT EXISTS 'kaggle_user'@'%' IDENTIFIED BY 'kaggle_password_123';
GRANT ALL PRIVILEGES ON kaggle_project.* TO 'kaggle_user'@'%';
FLUSH PRIVILEGES;