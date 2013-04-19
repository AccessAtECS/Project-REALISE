-- -----------------------------------------------------
-- SQL script to create REALISE website
-- Created 19/04/2013
-- -----------------------------------------------------


SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `realise` DEFAULT CHARACTER SET utf8 ;
USE `realise` ;

-- -----------------------------------------------------
-- Table `realise`.`category`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`category` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `username` VARCHAR(100) NULL DEFAULT NULL ,
  `hash` VARCHAR(100) NULL DEFAULT NULL ,
  `linkedinID` VARCHAR(20) NULL DEFAULT NULL ,
  `tagline` VARCHAR(100) NULL DEFAULT NULL ,
  `picture` VARCHAR(250) NULL DEFAULT NULL ,
  `email` VARCHAR(150) NULL DEFAULT NULL ,
  `bio` TEXT NULL DEFAULT NULL ,
  `admin` INT(1) NULL DEFAULT '0' ,
  `emailPublic` INT(1) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`idea`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`idea` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `overview` VARCHAR(250) NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `image` VARCHAR(100) NULL DEFAULT NULL ,
  `category_id` INT(11) NOT NULL ,
  `hidden` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id` (`user_id` ASC) ,
  INDEX `category_id` (`category_id` ASC) ,
  CONSTRAINT `idea_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `realise`.`user` (`id` )
    ON UPDATE CASCADE,
  CONSTRAINT `idea_ibfk_2`
    FOREIGN KEY (`category_id` )
    REFERENCES `realise`.`category` (`id` )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`project`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`project` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `overview` VARCHAR(250) NOT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `url` VARCHAR(200) NULL DEFAULT NULL ,
  `license` VARCHAR(100) NULL DEFAULT NULL ,
  `incubated` TINYINT(1) NOT NULL DEFAULT '0' ,
  `image` VARCHAR(100) NULL DEFAULT NULL ,
  `category_id` INT(11) NOT NULL ,
  `community_url` VARCHAR(200) NULL DEFAULT NULL ,
  `scm_url` VARCHAR(200) NULL DEFAULT NULL ,
  `repo_url` VARCHAR(200) NULL DEFAULT NULL ,
  `hidden` INT(1) NOT NULL DEFAULT '0' ,
  `openness_rating` INT(11) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `category_id` (`category_id` ASC) ,
  CONSTRAINT `project_ibfk_1`
    FOREIGN KEY (`category_id` )
    REFERENCES `realise`.`category` (`id` )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`comment`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`comment` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `project_id` INT(11) NULL DEFAULT NULL ,
  `idea_id` INT(11) NULL DEFAULT NULL ,
  `body` TEXT NOT NULL ,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`, `user_id`) ,
  INDEX `fk_comment_user1` (`user_id` ASC) ,
  INDEX `fk_comment_project1` (`project_id` ASC) ,
  INDEX `fk_comment_idea1` (`idea_id` ASC) ,
  CONSTRAINT `fk_comment_idea1`
    FOREIGN KEY (`idea_id` )
    REFERENCES `realise`.`idea` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comment_project1`
    FOREIGN KEY (`project_id` )
    REFERENCES `realise`.`project` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comment_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `realise`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(60) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`idea_project`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`idea_project` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `idea_id` INT(11) NOT NULL ,
  `project_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `idea_id` (`idea_id` ASC) ,
  INDEX `project_id` (`project_id` ASC) ,
  CONSTRAINT `idea_project_ibfk_1`
    FOREIGN KEY (`idea_id` )
    REFERENCES `realise`.`idea` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `idea_project_ibfk_2`
    FOREIGN KEY (`project_id` )
    REFERENCES `realise`.`project` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`license`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`license` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `url` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`open_question`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`open_question` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `question` TEXT NULL DEFAULT NULL ,
  `max_score` INT(3) NULL DEFAULT '5' ,
  `section` VARCHAR(80) NULL DEFAULT NULL ,
  `sub_question` TEXT NULL DEFAULT NULL ,
  `help` TEXT NULL DEFAULT NULL ,
  `type` VARCHAR(45) NOT NULL DEFAULT 'drop' ,
  `has_dont_know_answer` INT(3) NULL DEFAULT '1' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 56
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`open_answer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`open_answer` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `value` INT(3) NULL DEFAULT NULL ,
  `answer` VARCHAR(400) NULL DEFAULT 'Don''t know' ,
  `open_question_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`, `open_question_id`) ,
  INDEX `fk_open_answer_open_question1` (`open_question_id` ASC) ,
  CONSTRAINT `open_answer_ibfk_1`
    FOREIGN KEY (`open_question_id` )
    REFERENCES `realise`.`open_question` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 168
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`open_project_has_answer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`open_project_has_answer` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `value` TEXT NULL DEFAULT NULL ,
  `project_id` INT(11) NOT NULL ,
  `question_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_open_project_has_answer_open_answer1` (`question_id` ASC) ,
  INDEX `project_id` (`project_id` ASC) ,
  CONSTRAINT `fk_open_project_has_answer_open_answer1`
    FOREIGN KEY (`question_id` )
    REFERENCES `realise`.`open_answer` (`open_question_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `open_project_has_answer_ibfk_1`
    FOREIGN KEY (`project_id` )
    REFERENCES `realise`.`project` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`project_user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`project_user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `project_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `role` INT(11) NULL DEFAULT '1' ,
  PRIMARY KEY (`id`) ,
  INDEX `project_id` (`project_id` ASC) ,
  INDEX `user_id` (`user_id` ASC) ,
  CONSTRAINT `project_user_ibfk_1`
    FOREIGN KEY (`project_id` )
    REFERENCES `realise`.`project` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `project_user_ibfk_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `realise`.`user` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`tag`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`tag` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(150) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`tag_idea`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`tag_idea` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `tag_id` INT(11) NOT NULL ,
  `idea_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `tag_id` (`tag_id` ASC) ,
  INDEX `idea_id` (`idea_id` ASC) ,
  CONSTRAINT `tag_idea_ibfk_1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `realise`.`tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `tag_idea_ibfk_2`
    FOREIGN KEY (`idea_id` )
    REFERENCES `realise`.`idea` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`tag_project`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`tag_project` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `tag_id` INT(11) NOT NULL ,
  `project_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `tag_id` (`tag_id` ASC) ,
  INDEX `project_id` (`project_id` ASC) ,
  CONSTRAINT `tag_project_ibfk_1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `realise`.`tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `tag_project_ibfk_2`
    FOREIGN KEY (`project_id` )
    REFERENCES `realise`.`project` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `realise`.`user_group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `realise`.`user_group` (
  `user_id` INT(11) NOT NULL ,
  `group_id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_id`, `group_id`) ,
  INDEX `fk_user_has_group_group2` (`group_id` ASC) ,
  INDEX `fk_user_has_group_user2` (`user_id` ASC) ,
  CONSTRAINT `fk_user_has_group_group2`
    FOREIGN KEY (`group_id` )
    REFERENCES `realise`.`group` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_group_user2`
    FOREIGN KEY (`user_id` )
    REFERENCES `realise`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `realise`.`license`
-- -----------------------------------------------------
START TRANSACTION;
USE `realise`;
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (1, 'Academic Free License 3.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (2, 'Adaptive Public License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (3, 'Affero GNU Public License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (4, 'Apache License 2.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (5, 'Artistic License 2.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (6, 'Attribution Assurance Licenses', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (7, 'Boost Software License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (8, 'Common Development and Distribution License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (9, 'Common Public Attribution License 1.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (10, 'Common Public License 1.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (11, 'Eclipse Public License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (12, 'Educational Community License Version 2.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (13, 'Eiffel Forum License v2.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (14, 'European Union Public License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (15, 'Fair License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (16, 'GNU General Public License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (17, 'GNU General Public License v3.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (18, 'GNU Library or \'Lesser\' General Public License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (19, 'GNU Library or \'Lesser\' General Public License v3.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (20, 'Historical Permission Notice and Disclaimer', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (21, 'IPA Font License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (22, 'ISC License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (23, 'Lucent Public License Version 1.02', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (24, 'MIT license', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (25, 'Microsoft Public License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (26, 'Microsoft Reciprocal License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (27, 'MirOS License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (28, 'Mozilla Public License 1.1', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (29, 'NASA Open Source Agreement 1.3', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (30, 'NTP License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (31, 'BSD licenses - New and Simplified', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (32, 'Non-Profit Open Software License 3.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (33, 'Open Font License 1.1', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (34, 'Open Group Test Suite License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (35, 'Open Software License 3.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (36, 'Qt Public License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (37, 'Reciprocal Public License 1.5', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (38, 'Simple Public License 2.0', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (39, 'University of Illinois/NCSA Open Source License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (40, 'X.Net License', NULL);
INSERT INTO `realise`.`license` (`id`, `name`, `url`) VALUES (41, 'zlib/libpng license', NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `realise`.`open_question`
-- -----------------------------------------------------
START TRANSACTION;
USE `realise`;
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (1, 'Project Name', 5, 'info', 'Please provide a project name.', 'A name is needed to label the idea but it can be changed at each stage. <a href=\"/resources/faq#create-ans1\" target=\"_blank\">FAQ</a>', 'text', 0);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (2, 'Project URL', 5, 'info', 'Please provide a URL for the project.', 'You do not need to have a website as you add an idea but consider this when moving an idea into the incubator. <a href=\"/resources/faq#create-ans6\" target=\"_blank\">FAQ</a>', 'text', 0);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (3, 'Contact name', 5, 'info', 'Please provide a contact name for the project.', 'This is not essential when adding an idea but necessary when an idea moves to the incubator. <a href=\"/resources/faq#create-ans7\" target=\"_blank\">FAQ</a>', 'text', 0);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (4, 'Contact e-mail', 5, 'info', 'Please provide a contact email for the project.', 'This is not essential when adding an idea but necessary when an idea moves to the incubator. <a href=\"/resources/faq#create-ans8\" target=\"_blank\">FAQ</a>', 'text', 0);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (5, 'Is the licence recognised as a common Free or Open Source licence?', 3, 'legal', 'If the licence has been recognised by either of these bodies, it is more likely to have been assessed and found to be relatively open than a new licence or one which has not been OSI or FSF approved.', 'You need to think about this as you enter the incubator stage and this may be a developer\'s task. If you leave the issue of the type of licence too late problems may arise - ask an expert. <a href=\"/resources/faq#leg-ans5\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (6, 'Who has permission to run the software?', 0, 'legal', 'If the right to run the software is limited, that limits the recipient base of the software and thus openness is limited.', 'You do not need to think about who can use your software at the idea stage but will need to discuss this with a developer, funder and community around your project when you are licensing the product. <a href=\"/resources/faq#leg-ans1\" target=\"_blank\">FAQ</a>↵', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (7, 'Are all project dependencies clearly documented and licence compatibilities audited?', 5, 'legal', 'Crediting dependencies and conforming to their licence requirements is critical to ensuring the project conforms to all legal requirements placed on a FOSS project.', '<a href=\"http://en.wikipedia.org/wiki/Free_and_open-source_software\" target=\"_blank\" target=\"_blank\">Free and Open source software (FOSS)<a/> can use code owned by other people or organisations - each piece of code needs to be documented and attributed to the developer or owner(s). <a href=\"/resources/faq#leg-ans6\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (8, 'Who is permitted to examine the human-readable source code of the software?', 5, 'legal', 'Access to the source code is related to the trustworthiness, the sustainability, the ability to participate in and many other aspects of software.', 'A software program can be said to be open source if anyone can read the source code but there may still be restrictions on its use. <a href=\"/resources/faq#leg-ans7\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (9, 'Who is permitted to adapt or modify the source code of the software?', 5, 'legal', 'The right to modify or adapt the software makes the software more open for participation and applicability to different use cases.', 'Who can change or edit the code is down to the licence agreement and how open this has been made. <a href=\"/resources/faq#leg-ans3\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (10, 'Who is permitted to redistribute the modified or unmodified source code of the software?', 2, 'legal', 'If the right to redistribute changed software is limited then the benefits from being able to see and change the source are limited to personal use.', 'This is about being able to change as well as share source code with others so that you\'re project can be further developed.<a href=\"/resources/faq#leg-ans5\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (11, 'Does the licence permit sub-licensing of rights?', 3, 'legal', 'Sub-licensing of rights means the licensor is not bound to the rights given to them and may choose to change the rights according to their need. This may be useful in certain situations and can provide more open use of the software beyond the original licence intent. An example of sub-licensing of rights would be if code released under one licence could be redistributed in a modified or unmodified form under another licence with different rights. It doesn\'t include sub-licensing where rights are not modified.', 'Other people licensing your software under another licence - say a commercial licence or you changing a licence not to be taken lightly - answer with care. <a href=\"/resources/faq#leg-ans8\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (12, 'Does the licence also grant a patent licence to the licensee?', 3, 'legal', 'Patent waivers are built into some licences and can offer marginally more protection from patent litigation than no support, however the question is not weighted heavily as a patent waiver can only be given for patents for which the project has a right or licence, and there will inevitably be many thousands of patents that exist outside of the waiver offered. An example would be where each contributor of copyright material to the licensed code that is being distributed grants to the licensee a perpetual license to use that material without infringing any patent the contributor may hold against that contribution.', 'You can apply to the government for a patent - the right to own your idea or invention so that no one else can copy it. <a href=\"/resources/faq#leg-ans9\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (13, 'Is the licensee required to make modified or unmodified source code available if they redistribute code?', 5, 'legal', 'There is a difference between the right to access the source code, and the responsibility of the project to make the source code available. This question is to answer whether the latter is required.', 'It can be helpful to share code developments and updates but this may not be specified in the chosen licence. <a href=\"/resources/faq#leg-ans10\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (14, 'Is there full public disclosure of the majority of data and communication formats used in the project?', 5, 'standards', 'Full public disclosure of formats is the only practical way someone can implement the standard properly in another system. It is also the only way to future-proof data and systems and ensure there is always a mechanism to replicate and access the data or systems.', 'It is important to say how something has been written and to document this so that the development can continue on different platforms or systems. <a href=\"/resources/faq#acc-ans3\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (15, 'Does the project rely on any closed proprietary standards?', 5, 'standards', 'Proprietary standards can limit a project\'s potential and interoperability. There are of course some projects that use proprietary standards for interoperability, however this question is about whether a project could not work without the proprietary standard depended upon.', 'If the project makes use of any proprietary software there may be aspect of the code that cannot be re-used, viewed or adapted. <a href=\"/resources/faq#acc-ans4\" target=\"_blank\">FAQ</a>↵', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (16, 'Are there any direct costs associated with any standards used?', 5, 'standards', 'Costs associated with either acquiring the standard documentation or in implementing the standard are a barrier to entry that limits the potential of the standard.', 'Some proprietary standards have cost implications - the need to buy a licence or use a particular service. <a href=\"/resources/faq#acc-ans5\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (17, 'Are the majority of standards used approved and published by any of the following standards bodies -W3C, IEEE, IETF, OASIS, OR ISO?', 3, 'standards', 'Industry, de facto and published standards such as the Microsoft doc format can be popular, however arguably less open than a standard which has gone through a process of peer review, support and publication by a trusted and international standards organisation.', 'The standards mentioned range from standards used for writing web pages to those used for all types of products. <a href=\"/resources/faq#acc-ans6\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (18, 'Does the project use documented project management processes such as XP, SCRUMM or Prince 2?', 3, 'standards', 'Managed processes make it much easier for third parties to see where they can engage with the project.', 'Project management with timelines and workpackages are part of software development and there are systems that can help with these processes. <a href=\"/resources/faq#res-ans6\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (19, 'Does the project support Unicode through the use of encoding like UTF8?', 2, 'standards', 'Unicode support means better opportunity for multiple language support.', 'This is a question for the developer about the type of code used in the software application. <a href=\"/resources/faq#res-ans7\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (20, 'Which publicly available communication or dissemination mechanisms does the project use?', 5, 'knowledge', 'Multiple documentation and communication components are indicative of at least the opportunity for project knowledge to exist. There are certainly cases where too many avenues of knowledge can hurt a project.', 'Promoting a project is very important and a wide range of systems need to be used, but judiciously targeted - start early and build a community. <a href=\"/resources/faq#res-ans8\" target=\"_blank\">FAQ</a>', 'multi-select', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (21, 'Does the project discourage major project communication outside the approved channels selected above?', 3, 'knowledge', 'If major project communications are encouraged to be done through the main channels, then the chance to lose major decision making processes and information dissemination in private conversations is limited.', 'This is about making sure decisions made about the project are done so in an easily accessible public arena. <a href=\"/resources/faq#res-ans9\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (22, 'Are project decisions ever made in a non-public environment?', 3, 'knowledge', 'Making a decision in an environment that is not accessible to all interested parties limits awareness of those decisions and prevents participation in the decision making process.', 'Making all decisions in public may appear daunting but on many occasions can help development as support may be at hand. <a href=\"/resources/faq#res-ans10\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (23, 'Is any project knowledge purposely kept private?', 3, 'knowledge', 'The intent to keep knowledge private is not great for knowledge openness, however there may be specific reasons to keep the knowledge private, such as is the case for legal or privacy concerns.', 'Keeping things private may impact on how open your project is in terms of the ideals of open source software but there are times when you need to think carefully about the amount of information provided in public. <a href=\"/resources/faq#res-ans11\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (24, 'Who is able to access all the (non-private) project knowledge?', 5, 'knowledge', 'Apart from any knowledge defined as private, the ability for anyone to acquire project knowledge is important to their ability to participate as well as for the sustainability of the project.', 'Knowledge about the project needs to be easy to access and accessible is the widest sense of the word. <a href=\"/resources/faq#res-ans12\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (25, 'Is there any financial or legal barrier to accessing or acquiring some or all knowledge in the project?', 5, 'knowledge', 'If there is any legal or financial barrier to accessing knowledge, that provides a barrier to entry and participation, and thus makes the project less open. Note that requiring the purchase of proprietary software in order to access project knowledge is an example of a financial barrier, as is the need to pay for access.', 'If registration or downloads have cost implications this may affect open innovation but may be necessary if components of the program are costly such as licensed synthesised voices for text to speech. <a href=\"/resources/faq#res-ans13\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (26, 'Is there any technological barrier to accessing or acquiring some or all knowledge in the project?', 5, 'knowledge', 'If there is any technological barrier to accessing knowledge, then that provides a barrier to entry and participation, and thus makes the project less open. Examples of such barriers include DRM or deliberate limitation to a specific operating system.', '<a href=\"http://en.wikipedia.org/wiki/Digital_rights_management\">Digital Rights Management</a> such as intellectual property rights and copyright can impact on open innovation and distribution. <a href=\"/resources/faq#res-ans14\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (27, 'Is the knowledge stored in publically published data formats (with appropriate metadata) that will make it accessible over time?', 5, 'knowledge', 'If the knowledge is stored in proprietary formats then it is more likely the data won\'t be accessible in the long term which is a risk to the long term openness of the project.', 'Using <href=\"http://en.wikipedia.org/wiki/Metadata\">metadata</a> that provides machine readable information means your project data will be found more easily by search engines and if a commonly used format is used more people will access the information. <a href=\"/resources/faq#res-ans15\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (28, 'Is any of the project knowledge available in more than one language?', 3, 'knowledge', 'The previous questions are determining whether there are any artificial limitations to accessing project knowledge.', 'This can be achieved via automatic or human translation with a symbol on the web pages showing which languages are available. <a href=\"/resources/faq#res-ans16\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (29, 'Who is able to contribute to the project knowledge?', 5, 'knowledge', 'Understanding who can contribute to the knowledge is a good way of understanding the potential for participation in the knowledge.', 'Clearly offering links to blogs, wikis, mailng lists and access to source code that is open to everyone allows for increased interaction with a project. <a href=\"/resources/faq#res-ans17\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (30, 'Are there public archives of the project knowledge?', 3, 'knowledge', 'If there is a single point of loss or failure in the knowledge base, then projects put themselves at risk of massive and possible permanent interruption.', 'Not being able to trace information about a project makes it much harder for individuals to take part in the development. <a href=\"/resources/faq#res-ans18\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (31, 'Are there documented data recovery processes in place?', 5, 'knowledge', 'Without clearly documented processes for data recovery it is more likely that data archiving and recovery will have full coverage. In addition, reinstating data in the event of failure will be slower and potentially flawed without a documented process to guide recovery.', 'In the case of data recovery it is important to have some policies and procedures in place so that everyone knows how they can recover the data should disaster occur. It may be there are certain individuals involved and they will need access to the back up plans.  Documents must clearly lay out the processes and these should be available to the groups you have chosen to inform. <a href=\"/resources/faq#res-ans19\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (32, 'How good is the user specific public documentation?', 5, 'knowledge', 'The usability of knowledge is a difficult thing to measure. This could probably be captured through extensive subjective questions, however asking people to rate the quality is a good start to understanding and differentiating between base standards of good documentation and projects that really excel in this area.', 'Good documentation for users should include help files, contact details and legal documents, accessibility statements etc. Community members should be clearly invited to contribute to the documentation (e.g.feedback) and an obvious channel for doing this should accessible.  JISC TechDis provide guidelines for making accessible documents. <a href=\"/resources/faq#res-ans20\" target=\"_blank\">FAQ</a>', 'scale', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (33, 'How good is the developer specific public documentation?', 5, 'knowledge', 'The usability of knowledge is a difficult thing to measure. This could probably be captured through extensive subjective questions, however asking people to rate the quality is a good start to understanding and differentiating between base standards of good documentation and projects that really excel in this area.', 'Good developer documentation should be easy to find from the main project website and clearly detailed as to it purpose.  Maltblue provide 5 examples of good development documentation. <a href=\"/resources/faq#res-ans21\" target=\"_blank\">FAQ</a>', 'scale', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (34, 'Are there documentation sources external to the project?', 3, 'knowledge', 'The more external sources of documentation, the more knowledge there is available that is likely done professionally or to cater for extra use cases. This could be external community documentation or professional publications.', 'It is easier to manage and for community members to track what is happening in any project if everything is recorded at one location. Duplication of some information in external sources is acceptable provided there is a reference to (and ideally links back to) the project website. <a href=\"/resources/faq#res-ans22\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (35, 'Is there clear leadership in the project?', 5, 'governance', 'Leadership may be an individual or group such as a board. Clear leadership means a project has a better chance of clear direction and purpose, and is more likely to avoid leadership contesting and the sort of committee based decision making which can slow a project down to a grinding halt.', 'It helps to clearly say who is involved in a project, who is the main contact and how people can contribute to a project.  Names and faces can make people more engaged with a project. <a href=\"/resources/faq#leg-ans11\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (36, 'Are the structure and policies of the project clearly and publically documented?', 3, 'governance', 'Public documentation of a project structure and policies increase transparency and trust in a project. This should include all of leadership structure, decision making proceses, the process for becoming a contributor and maintainer and the licence of the software.', 'For anyone who wishes to get involved in a project  its important they know how it works, who does what and what its goals are. For others it may just be of passing interest. There needs to be a page that shows who belongs to the community and how this impacts on the project. <a href=\"/resources/faq#leg-ans12\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (37, 'Are there publically accessible behavioural guidelines for the project?', 3, 'governance', 'Provides a public reference by which project members are held accountable. Encourages a good working environment that is productive and welcoming to newcomers.', 'As with most online communities no one wants there to be any offence to other users by unacceptable behaviour. It is a good idea to have a document on your project site that stipulates the type of behaviour you expect and it may include some of the points provided by <a href=\"http://www.wikihow.com/Behave-On-an-Internet-Forum\" target=\"_blank\">Wikihow</a>. <a href=\"/resources/faq#leg-ans13\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (38, 'Is there publically accessible and easy to find documentation about how to participate in the project?', 3, 'governance', 'The availability of such documentation encourages use and contributions to a project, so it is important to facilitate new interest in a public fashion. If a project doesn\'t make this available it simply makes it more difficult to get involved in any capacity.', 'On Realise it has been made very easy for participants to contribute via the comment boxes on entries already present or by adding a completely new idea. You may want to contribute by taking on the management of an idea and moving it into the incubator or project areas. These are the sort of easy ways you can encourage people to contribute on any project by providing comment areas, mail lists and forums. <a href=\"/resources/faq#leg-ans14\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (39, 'Is the project leadership elected by the project community?', 3, 'governance', 'Elected leadership indicates a more openly participatory project. It is true that many projects have only one maintainer, and thus they are handicapped by this question, however it is also true that smaller project do not require as open a governance as larger projects.', 'To remain an open community it is important that the members can impact how it evolves and the decisions taken. Typically however, only people who have demonstrated commitment and interest to projects are given votes. So while everyone can join in the discussions only those with a vote can affect the decisions. In Realise activities reaching the incubator stage will be expected to quickly make it clear how project leadership will be managed once the activity reaches project status. <a href=\"/resources/faq#leg-ans15\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (40, 'Who is able to contribute to the project development?', 1, 'governance', 'Contributing to the software is defined as any activity which adds to the project. Includes code, patches, bug reports, documentation, and translation. A project may choose to limit who can contribute to a project for various reasons of control, however this limits the potential of the project. Note that we do not mean \"who can gain write access to the resources\", contributions can be in the form of ideas, documentation, patches, bug reports, feature requests, testing, major software additions etc.', 'Anyone can contribute to Realise and its hosted ideas, incubators or projects and with open innovation and truly open source development this should be the case with most projects. <a href=\"/resources/faq#leg-ans16\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (41, 'Are contributors required to sign a document stating they have the necessary permissions to make their contributions?', 3, 'governance', 'It is critical for a project to ensure that all code can be released legally under the chosen licence. Contributor licence agreements enable the project to show they have taken all reasonable steps to ensure this is the case.', 'Some people who share content in open project communities (including Realise) may  be doing so from work. These people may need to prove they have permission from their employer that the content they give is available freely for anyone who uses the website.  This means their employer loses any ownership of the content. Note that Realies\'s default is that all content is public as anything added is automatically accessible by the public. <a href=\"/resources/faq#leg-ans17\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (42, 'Who is able to obtain commit rights to the project development?', 5, 'governance', 'This is a person who commits code/changes to the primary project source. Understanding who is able to become a committer is indicative to the openness of a project to share responsibility at the code level. By \"commit rights\" we mean those who have write access to project resources.', 'This may be something that is decided by the community or the leading group of developers and if it is more about outside developers contributing it may mean that there is a need for \'Contributor Licence Agreements\' . <a href=\"/resources/faq#leg-ans18\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (43, 'Is there a single point of failure or control for committing changes to the primary project source?', 5, 'governance', 'Single point of failure/control refers to both the case of a single individual and the case where all committers work for the one company. A single point of failure/control for committing changes to the codebase provides the opportunity for massive project disruption, whether it be through an individual not having time or in all committers working for the one company and introducing the risk of hostile takeover. If there is a single point of failure, either individual or company-wise, a documented succession plan can make the difference between seamless progress of the project or a major disruption.', 'These are two very different ways of working on large projects - the Distributed revision control (DRCS) allows developers to commit to many different systems that are then synchronised by a series of patches whereas the centralised approach which as a single repository for version control. <a href=\"/resources/faq#leg-ans19\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (44, 'Who is able to obtain easy access to and use the software?', 5, 'governance', 'The terms of use in the licence of a project does not mean that in actual practice the software is publicly and openly available for public use. the more barriers to entry for use of the software, the less open.', 'Anyone who joins the open community should have both easy access and use of the software - this is the whole point of the open source approach. Restrictions or limitations on this significantly reduces the openness of the project. Restrictions or limitations will be individual to each project. <a href=\"/resources/faq#leg-ans20\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (45, 'Is the software easy for users to access, install and run so it can be trialled (for those who have access)?', 5, 'governance', 'Enabling users to quickly download, install and test a software product ensures that the project generates additional interest in the project and thus increase the chances of new contributors being attracted to the project.', 'Today everyone expects software to work in an easy way and quickly become frustrated with software that isn\'t. If software is difficult to access, install or run it makes it difficult for users to join in and contribute to the community. And for end users, for them to get the benefits of the software. In addition if users will be trialling software they must be able to remove it easily to go back to what they had before. Otherwise the trial could be seen as harmful. <a href=\"/resources/faq#leg-ans21\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (46, 'The software release cycle (including snapshots and major releases) is ....', 3, 'governance', 'A project that is predictable and consistent is more likely to encourage regular community participation and interest than a project that is inconsistent and unpredictable.', 'Receiving updates very frequently can be a real nuisance to  users of the software. More often updates being sent to programmers of prototype versions is likely to be more acceptable. <a href=\"/resources/faq#leg-ans22\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (47, 'Is it easy to acquire, build, configure and install the source code from scratch?', 3, 'governance', 'If the codebase is unable to be openly forked, then the project can be held ransom to bad leadership or hostile takeover.', 'Obviously if code is complex and installation takes too much time the project is going to falter.  Poor or a lack of documentation will also not help users. <a href=\"/resources/faq#leg-ans23\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (48, 'Is there an avenue and structure for recourse beyond the project maintainers?', 3, 'governance', 'If there is no avenue for recourse, the project relies on the good will of the maintainer, and thus opens up the likelihood of forking under bad leadership.', 'As disputes can occur in any human activity it makes sense that someone who can help resolve problems independently is available. <a href=\"/resources/faq#leg-ans24\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (49, 'Are there any costs or barriers to setting up a business around the project?', 5, 'market', 'The higher the costs of setup, the higher the barrier to entry for creating an open market around the project. Also a set cost is far less an overhead than ongoing costs such as royalties or patents.', 'There may be barriers to others making money from any particular project and it is important to connect with the community to see how it all worked.  But Realise does not stop commercialisation of ideas and projects and indeed hopes that businesses may be formed from the incubators or projects. <a href=\"/resources/faq#leg-ans25\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (50, 'Are there any technical barriers of entry to setting up a business around the project?', 5, 'market', 'Technical barriers to entry, such as DRM or proprietary hardware, reduce the ease of building an open market around the project.', 'Technical barriers would need to be discussed with developers but with most open source projects it is assumed that these discussions would lead to satisfactory business models developing. <a href=\"/resources/faq#leg-ans26\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (51, 'What proportion of the core developers are from the one company, institution or department?', 5, 'market', 'If so this gives one company a potential market advantage over competitors.', 'This is all about who contributes to a project and may vary depending on the size and type of project.  What is important is that communication between all contributors is open and transparent to achieve the most open innovation. <a href=\"/resources/faq#leg-ans27\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (52, 'How many contributors have some or all of the time they spend on the software paid for?', 5, 'market', 'The more contributors that are able to work on a project with business support, generally the more market ready it is.', 'People outside of or new to open innovation or software communities often make the mistake that everyone involved in open work is doing everything for free. In fact software programmers most of the time get paid to develop the programs. Realise advocates the following approach, where a project activity is going to be funded that that funding is offered to all the people involved in that activity NOT just the programmer. <a href=\"/resources/faq#leg-ans28\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (53, 'Is the project applicable to more than one industry?', 5, 'market', 'If the software is only applicable to one industry, the market opportunities are limited.', 'If a project has more than one use then the chances of success are greater. However its far from essential. <a href=\"/resources/faq#leg-ans29\" target=\"_blank\">FAQ</a>', 'drop', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (54, 'Which revenue models are available to a new business looking to build a revenue steam around the project?', 5, 'market', 'The more revenue models that are available the better the opportunity for building a market. The more businesses that are already involved, the more the project is already succeeding in the broader market space.', 'The more ways businesses can make money from the project software the better because it means there are more chances of funding its community and sustaining the software. However just one model is fine if it is successful enough. <a href=\"/resources/faq#leg-ans30\" target=\"_blank\">FAQ</a>', 'multi-select', 1);
INSERT INTO `realise`.`open_question` (`id`, `question`, `max_score`, `section`, `sub_question`, `help`, `type`, `has_dont_know_answer`) VALUES (55, 'How many organisations offer commercial software development and code customisation services on the project?', 5, 'market', 'The more business are already offering services around the project, the more open it most likely is for a new project to come along and build a business.', 'This can be very helpful in terms of training, systems support, hosting and further development and may not affect the open source nature of the project. <a href=\"/resources/faq#leg-ans31\" target=\"_blank\">FAQ</a>', 'drop', 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `realise`.`open_answer`
-- -----------------------------------------------------
START TRANSACTION;
USE `realise`;
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (1, 5, 'text-reserved', 1);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (2, 5, 'text-reserved', 2);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (3, 5, 'text-reserved', 3);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (4, 5, 'text-reserved', 4);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (5, 3, 'Proprietary', 5);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (6, 3, 'OSI approved', 5);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (7, 3, 'Recognised by FSF', 5);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (8, 3, 'Both OSI and FSF approved', 5);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (9, 1, 'A specific group', 6);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (10, 1, 'A specfic user', 6);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (11, 5, 'Anyone', 6);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (12, 1, 'No', 7);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (13, 2, 'Yes, but the audit process is undocumented', 7);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (14, 3, 'Yes, there is a documented audit process', 7);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (15, 5, 'Yes, there is a documented audit process which is followed before each release', 7);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (16, 3, 'A specified sub-set', 8);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (17, 3, 'A specified super-set', 8);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (18, 5, 'Anyone', 8);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (19, 3, 'A specified sub-set', 9);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (20, 3, 'A specified super-set', 9);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (21, 5, 'All licencees', 9);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (22, 2, 'A specified sub-set', 10);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (23, 2, 'A specified super-set', 10);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (24, 5, 'All licencees', 10);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (25, 0, 'No', 11);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (26, 3, 'Yes, but with conditions', 11);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (27, 3, 'Yes, unconditionally', 11);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (28, 0, 'No', 12);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (29, 3, 'Yes', 12);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (30, 0, 'No', 13);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (31, 3, 'Sometimes', 13);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (32, 5, 'Yes', 13);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (33, 0, 'No', 14);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (34, 5, 'Yes', 14);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (35, 0, 'No', 15);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (36, 5, 'Yes', 15);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (37, 0, 'Implementation costs', 16);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (38, 2, 'Acquisition costs', 16);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (39, 5, 'No costs', 16);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (40, 0, 'No', 17);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (41, 1, 'Some', 17);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (42, 3, 'Yes', 17);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (43, 0, 'No', 18);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (44, 3, 'Yes', 18);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (45, 0, 'No', 19);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (46, 2, 'Yes', 19);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (47, NULL, 'Documentation section of website', 20);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (48, NULL, 'Design documents', 20);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (49, NULL, 'Project roadmap', 20);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (50, NULL, 'Machine readable meta-data', 20);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (51, NULL, 'Publicly writeable wiki', 20);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (52, NULL, 'Version control system', 20);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (53, NULL, 'Email lists or online forums', 20);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (54, NULL, 'Instant messaging/IRC', 20);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (55, NULL, 'Issue Tracker', 20);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (56, 0, 'No', 21);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (57, 3, 'Yes', 21);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (58, 3, 'No', 22);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (59, 1, 'Yes, but only via a semi-private mechanism', 22);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (60, 0, 'Yes', 22);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (61, 3, 'No', 23);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (62, 1, 'Yes, but only for legal or privacy issues', 23);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (63, 0, 'Yes', 23);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (64, 0, 'A subset of participants', 24);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (65, 3, 'All participants, including users', 24);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (66, 5, 'Anyone', 24);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (67, 5, 'No', 25);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (68, 2, 'Yes, but only private data', 25);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (69, 0, 'Yes', 25);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (70, 5, 'No', 26);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (71, 2, 'Yes, some knowledge or technology required', 26);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (72, 0, 'Yes', 26);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (73, 0, 'No', 27);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (74, 2, 'Some knowledge is', 27);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (75, 5, 'Yes', 27);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (76, 0, 'No', 28);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (77, 3, 'Yes', 28);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (78, 0, 'Only a closed group', 29);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (79, 3, 'Only project participants', 29);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (80, 5, 'Anyone', 29);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (81, 0, 'No publicly available archives', 30);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (82, 1, 'Some publicly available archives', 30);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (83, 3, 'Publicly available archives of all materials', 30);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (84, 2, 'Yes, but only for some data', 31);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (85, 5, 'Yes', 31);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (86, 0, '0', 32);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (87, 1, '1', 32);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (88, 2, '2', 32);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (89, 3, '3', 32);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (90, 4, '4', 32);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (91, 5, '5', 32);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (92, 0, '0', 33);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (93, 1, '1', 33);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (94, 2, '2', 33);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (95, 3, '3', 33);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (96, 4, '4', 33);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (97, 5, '5', 33);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (98, 3, 'No', 34);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (99, 1, 'Yes', 34);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (100, 0, 'No', 35);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (101, 5, 'Yes', 35);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (102, 0, 'No governance documentation', 36);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (103, 1, 'Partial (includes one or more of the above items)', 36);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (104, 3, 'Yes (includes all of the above items)', 36);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (105, 0, 'No', 37);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (106, 3, 'Yes', 37);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (107, 0, 'No', 38);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (108, 1, 'Yes, but only for users or developers', 38);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (109, 3, 'Yes, for both users and developers', 38);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (110, 2, 'No', 39);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (111, 3, 'Yes', 39);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (112, 1, 'A closed group', 40);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (113, 3, 'Participants willing to register in some way', 40);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (114, 5, 'Anyone', 40);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (115, 0, 'No', 41);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (116, 1, 'Yes, but only those with write access to resources', 41);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (117, 3, 'Yes, for all contributions containing significant IP', 41);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (118, 1, 'A self appointed group', 42);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (119, 3, 'Anyone who earns sufficient merit', 42);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (120, 5, 'Partially self appointed, partially meritocratic group', 42);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (121, 0, 'Anyone', 42);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (122, 0, 'No', 43);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (123, 2, 'Yes, with one person having access', 43);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (124, 5, 'Yes, with more then 1 person having access', 43);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (125, 0, 'Only a specific group', 44);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (126, 3, 'A super-set of people', 44);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (127, 5, 'Anyone', 44);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (129, 0, 'No', 45);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (130, 3, 'Yes, although some local configuration and setup is required', 45);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (131, 5, 'Yes, there is a fully automated installer', 45);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (132, 0, 'Inconsistent and unpredictable', 46);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (133, 2, 'Inconsistent or unpredictable', 46);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (134, 3, 'Consistent and predictable', 46);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (135, 0, 'No', 47);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (136, 1, 'Yes, with technical or access limitations', 47);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (137, 3, 'Yes', 47);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (138, 0, 'No', 48);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (139, 3, 'Yes', 48);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (140, 5, 'No', 49);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (141, 3, 'Yes, a fixed fee', 49);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (142, 0, 'Yes, on a revenue basis', 49);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (143, 5, 'No', 50);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (144, 3, 'Yes, some', 50);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (145, 0, 'Yes', 50);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (146, 0, '1', 51);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (147, 3, '2', 51);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (148, 5, '3 or more', 51);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (149, 1, 'A minority', 52);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (150, 3, 'A majority', 52);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (151, 5, 'All', 52);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (152, 0, 'Not viable', 53);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (153, 1, 'Not viable, but will be with further development', 53);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (154, 3, 'Quite viable', 53);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (155, 5, 'Viable', 53);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (156, 1, 'Customisation', 54);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (157, 1, 'Support and maintenance', 54);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (158, 1, 'Hosted services', 54);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (159, 1, 'Implementation/deployment services', 54);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (160, 1, 'Training', 54);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (161, 1, 'Dual licensing', 54);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (162, 1, 'Localisation/internationalisation', 54);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (163, 1, 'Consulting', 54);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (164, 1, 'Proprietary extensions', 54);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (165, 0, '0', 55);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (166, 2, '1-2', 55);
INSERT INTO `realise`.`open_answer` (`id`, `value`, `answer`, `open_question_id`) VALUES (167, 5, '3 or more', 55);

COMMIT;
