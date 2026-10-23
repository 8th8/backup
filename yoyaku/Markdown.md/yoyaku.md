
### CREATE TABLE `tables` ----------------------------

`    CREATE TABLE `tables` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `table_name` VARCHAR(20) NOT NULL UNIQUE,
        `capacity` TINYINT UNSIGNED NOT NULL,
        `status` ENUM('available', 'maintenance')
            NOT NULL DEFAULT 'available',
        `note` VARCHAR(100) DEFAULT NULL,

        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
`

--------------------------------------------------------

### CREATE TABLE `reservations` ----------------------------

CREATE TABLE `reservations` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `reservation_code` VARCHAR(20) NOT NULL UNIQUE,
        `table_id` INT UNSIGNED DEFAULT NULL,
        `customer_name` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `reservation_date` DATE NOT NULL,
        `reservation_time` TIME NOT NULL,
        `party_size` TINYINT UNSIGNED NOT NULL,
        `note` TEXT DEFAULT NULL,
        `status` ENUM('pending', 'confirmed', 'cancelled')
            NOT NULL DEFAULT 'pending',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (`id`),

        CONSTRAINT `fk_reservations_table`
            FOREIGN KEY (`table_id`)
            REFERENCES `tables` (`id`)
            ON DELETE SET NULL
            ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


### CREATE TABLE `reservations` ----------------------------


CREATE TABLE `admins` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,                      
    `username` VARCHAR(50) NOT NULL UNIQUE,                         
    `password_hash` VARCHAR(255) NOT NULL,
    `display_name` VARCHAR(100) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_login_at` DATETIME DEFAULT NULL,

    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


### CREATE TABLE `business_hours ` ----------------------------

    CREATE TABLE `business_hours` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `weekday` TINYINT UNSIGNED NOT NULL,
        `is_open` TINYINT(1) NOT NULL DEFAULT 1,
        `open_time` TIME DEFAULT NULL,
        `close_time` TIME DEFAULT NULL,
        `slot_minutes` SMALLINT UNSIGNED NOT NULL DEFAULT 90,
        `max_party_size` TINYINT UNSIGNED NOT NULL DEFAULT 8,
        `cutoff_hours` SMALLINT UNSIGNED NOT NULL DEFAULT 2,

        PRIMARY KEY (`id`),
        UNIQUE KEY uq_weekday (weekday)
    );

### CREATE TABLE `closed_dates ` ----------------------------

