#Update Database
INSERT INTO `responsibilities_work` VALUES(NULL, 'paymethods', 'روش پرداخت', 'cart', 'yes', 'no', 'paymethods.view.php', 4, 'slink', '_self');
DROP TABLE IF EXISTS `paymethods`;
CREATE TABLE IF NOT EXISTS `paymethods` (
    `id` int NOT NULL AUTO_INCREMENT,
    `hits` int UNSIGNED DEFAULT '0',
    `fldname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    `fldcode1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    `fldcode2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    `fldcode3` text CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    `fldcode4` text CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    `fldcode5` text CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    `fldcode6` text CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    `fldcode7` text CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    `fldcode8` text CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci NOT NULL,
    `published` tinyint(1) NOT NULL,
    `ordering` int NOT NULL,
    `created` datetime NOT NULL,
    `created_by` int DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;