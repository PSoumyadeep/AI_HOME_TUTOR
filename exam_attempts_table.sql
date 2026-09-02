-- ------------------------------------------------------------
-- exam_attempts: records every MCQ test a user takes, so the
-- Progress page can show a calendar of exam days.
-- Run this once against the `gen_ai_project` database.
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `exam_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `user_class` varchar(50) DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `chapters` varchar(500) DEFAULT NULL,
  `total_questions` int NOT NULL DEFAULT 0,
  `correct` int NOT NULL DEFAULT 0,
  `wrong` int NOT NULL DEFAULT 0,
  `score_pct` int NOT NULL DEFAULT 0,
  `taken_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_taken_at` (`taken_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
