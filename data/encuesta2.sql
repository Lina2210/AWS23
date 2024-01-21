CREATE DATABASE IF NOT EXISTS encuesta2;
use encuesta2;

CREATE TABLE `User` (
    `user_id` integer NOT NULL PRIMARY KEY,
    `user_name` varchar(255) NOT NULL,
    `mail` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `tlfn` int NOT NULL,
    `country` varchar(255) NOT NULL,
    `city` varchar(255) NOT NULL,
    `postal_code` int NOT NULL
);

CREATE TABLE `Question` (
    `question_id` int NOT NULL PRIMARY KEY,
    `questionText` varchar(255) NOT NULL,
    `user_id` int NOT NULL,
    `start_date` date NOT NULL,
    `end_date` date NOT NULL,
    `state` varchar(255) NOT NULL,
    `creation` datetime NOT NULL
);

CREATE TABLE `Answer` (
    `answer_id` int NOT NULL PRIMARY KEY,
    `question_id` int NOT NULL,
    `question_text` varchar(255) NOT NULL
);

CREATE TABLE `UserVote` (
    `user_id` int NOT NULL,
    `answer_id` int NOT NULL,
    PRIMARY KEY (`user_id`, `answer_id`)
);

CREATE TABLE `UserQuestionAccess` (
    `user_id` int NOT NULL,
    `question_id` int NOT NULL,
    PRIMARY KEY (`user_id`, `question_id`)
);


ALTER TABLE `Question` ADD CONSTRAINT FK_Question_User
FOREIGN KEY (user_id)
    REFERENCES User (user_id);

ALTER TABLE `Answer` ADD CONSTRAINT FK_Answer_Question
FOREIGN KEY (question_id)
    REFERENCES Question (question_id);

ALTER TABLE `UserVote` ADD CONSTRAINT FK_UserVote_Answer
FOREIGN KEY (answer_id)
    REFERENCES Answer (answer_id);

ALTER TABLE `UserVote` ADD CONSTRAINT FK_UserVote_User
FOREIGN KEY (user_id)
    REFERENCES User (user_id);

ALTER TABLE `UserQuestionAccess` ADD CONSTRAINT FK_UserQuestionAccess_User
FOREIGN KEY (user_id)
    REFERENCES User (user_id);

ALTER TABLE `UserQuestionAccess` ADD CONSTRAINT FK_UserQuestionAccess_Question
FOREIGN KEY (question_id)
    REFERENCES Question (question_id);






-- ALTER TABLE `Question` ADD FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);

-- ALTER TABLE `Answer` ADD FOREIGN KEY (`question_id`) REFERENCES `Question` (`question_id`);

-- ALTER TABLE `UserQuestionAccess` ADD FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);

-- ALTER TABLE `UserQuestionAccess` ADD FOREIGN KEY (`question_id`) REFERENCES `Question` (`question_id`);

-- ALTER TABLE `UserVote` ADD FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);

-- ALTER TABLE `UserVote` ADD FOREIGN KEY (`answer_id`) REFERENCES `Answer` (`answer_id`);