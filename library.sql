-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Фев 07 2021 г., 14:14
-- Версия сервера: 10.5.8-MariaDB
-- Версия PHP: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `library3`
--

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `bookId` int(11) UNSIGNED NOT NULL,
  `Title` text NOT NULL,
  `description` text NOT NULL,
  `creatorId` int(11) UNSIGNED NOT NULL,
  `publisherId` int(11) UNSIGNED DEFAULT NULL,
  `yearOfPublishing` int(10) UNSIGNED NOT NULL,
  `PlaceOfPublication` text DEFAULT NULL,
  `languageId` int(11) UNSIGNED DEFAULT NULL,
  `identifier` text DEFAULT NULL,
  `typeId` int(11) UNSIGNED DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `creators`
--

CREATE TABLE `creators` (
  `creatorId` int(11) UNSIGNED NOT NULL,
  `LastName` text NOT NULL,
  `FirstName` text NOT NULL,
  `midleName` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `genres`
--

CREATE TABLE `genres` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `inventory`
--

CREATE TABLE `inventory` (
  `inventoryId` int(10) UNSIGNED NOT NULL,
  `bookID` int(10) UNSIGNED NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `itemGenres`
--

CREATE TABLE `itemGenres` (
  `bookId` int(11) UNSIGNED NOT NULL,
  `genresId` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `itemRights`
--

CREATE TABLE `itemRights` (
  `roleId` int(11) UNSIGNED NOT NULL,
  `rights` enum('userManagement','deleteBook','editBook','editDirectory','editCreator','report') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `itemRights`
--

INSERT INTO `itemRights` (`roleId`, `rights`) VALUES
(1, 'userManagement'),
(1, 'deleteBook'),
(1, 'editBook'),
(1, 'editCreator'),
(1, 'editDirectory'),
(1, 'report'),
(2, 'editBook'),
(3, 'userManagement');

-- --------------------------------------------------------

--
-- Структура таблицы `languages`
--

CREATE TABLE `languages` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `languages`
--

INSERT INTO `languages` (`id`, `name`, `deleted`) VALUES
(1, 'ru', 0),
(2, 'en', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `publishers`
--

CREATE TABLE `publishers` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `registery`
--

CREATE TABLE `registery` (
  `registeryid` int(11) UNSIGNED NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `expireDate` int(10) UNSIGNED DEFAULT NULL,
  `inventoryId` int(11) UNSIGNED NOT NULL,
  `userId` int(11) UNSIGNED NOT NULL,
  `location` int(10) UNSIGNED DEFAULT NULL,
  `act` text DEFAULT NULL,
  `state` enum('handedOut','reserved','available','decommissioned') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `roleId` int(10) UNSIGNED NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`roleId`, `name`) VALUES
(1, 'Все права'),
(2, 'Выдача книг'),
(3, 'Регистрация пользователей '),
(4, 'Обычный пользователь ');

-- --------------------------------------------------------

--
-- Структура таблицы `translation`
--

CREATE TABLE `translation` (
  `en` text NOT NULL,
  `ru` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `types`
--

CREATE TABLE `types` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `userId` int(11) UNSIGNED NOT NULL,
  `roleId` int(11) UNSIGNED DEFAULT NULL,
  `login` varchar(64) NOT NULL,
  `lastName` text NOT NULL,
  `firstName` text NOT NULL,
  `midleName` text NOT NULL,
  `password` char(60) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`bookId`),
  ADD KEY `books_languages_fk` (`languageId`),
  ADD KEY `books_creators_fk` (`creatorId`),
  ADD KEY `books_publishers_fk` (`publisherId`),
  ADD KEY `books_types_fk` (`typeId`);

--
-- Индексы таблицы `creators`
--
ALTER TABLE `creators`
  ADD PRIMARY KEY (`creatorId`);

--
-- Индексы таблицы `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Индексы таблицы `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventoryId`),
  ADD KEY `inventory_books_fk` (`bookID`);

--
-- Индексы таблицы `itemGenres`
--
ALTER TABLE `itemGenres`
  ADD KEY `itemGenres_books_fk` (`bookId`),
  ADD KEY `itemGenres_Genres_fk` (`genresId`);

--
-- Индексы таблицы `itemRights`
--
ALTER TABLE `itemRights`
  ADD KEY `itemRights` (`roleId`);

--
-- Индексы таблицы `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `registery`
--
ALTER TABLE `registery`
  ADD PRIMARY KEY (`registeryid`),
  ADD KEY `registery_users_fk` (`userId`),
  ADD KEY `registery_users_fk1` (`location`),
  ADD KEY `registery_inventory_fk` (`inventoryId`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`roleId`);

--
-- Индексы таблицы `translation`
--
ALTER TABLE `translation`
  ADD UNIQUE KEY `translation_en` (`en`) USING HASH;

--
-- Индексы таблицы `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD KEY `users_roles_fk` (`roleId`),
  ADD KEY `login_deletes_indx` (`login`,`deleted`) USING BTREE;

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `bookId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `creators`
--
ALTER TABLE `creators`
  MODIFY `creatorId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventoryId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `publishers`
--
ALTER TABLE `publishers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `registery`
--
ALTER TABLE `registery`
  MODIFY `registeryid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `roleId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `types`
--
ALTER TABLE `types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_creators_fk` FOREIGN KEY (`creatorId`) REFERENCES `creators` (`creatorId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `books_languages_fk` FOREIGN KEY (`languageId`) REFERENCES `languages` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `books_publishers_fk` FOREIGN KEY (`publisherId`) REFERENCES `publishers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `books_types_fk` FOREIGN KEY (`typeId`) REFERENCES `types` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_books_fk` FOREIGN KEY (`bookID`) REFERENCES `books` (`bookId`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `itemGenres`
--
ALTER TABLE `itemGenres`
  ADD CONSTRAINT `itemGenres_Genres_fk` FOREIGN KEY (`genresId`) REFERENCES `genres` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `itemGenres_books_fk` FOREIGN KEY (`bookId`) REFERENCES `books` (`bookId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `itemRights`
--
ALTER TABLE `itemRights`
  ADD CONSTRAINT `itemRights` FOREIGN KEY (`roleId`) REFERENCES `roles` (`roleId`);

--
-- Ограничения внешнего ключа таблицы `registery`
--
ALTER TABLE `registery`
  ADD CONSTRAINT `registery_inventory_fk` FOREIGN KEY (`inventoryId`) REFERENCES `inventory` (`inventoryId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `registery_users_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `registery_users_fk1` FOREIGN KEY (`location`) REFERENCES `users` (`userId`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_roles_fk` FOREIGN KEY (`roleId`) REFERENCES `roles` (`roleId`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
