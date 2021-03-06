-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3307
-- Время создания: Сен 01 2017 г., 19:35
-- Версия сервера: 5.5.50
-- Версия PHP: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `rest`
--

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `category`
--

INSERT INTO `category` (`id`, `name`, `image`) VALUES
(1, 'Home repairs', 'category_image/5964832fdaad3.jpg'),
(2, 'Car repairs', 'category_image/5964832fdabgad3.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE IF NOT EXISTS `favorites` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`id`, `post_id`, `user_id`) VALUES
(4, 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recepient_id` int(11) NOT NULL,
  `message` text,
  `image` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `message`
--

INSERT INTO `message` (`id`, `sender_id`, `recepient_id`, `message`, `image`, `status`, `date`) VALUES
(12, 49, 4, 'Hello!', NULL, 0, '2017-08-10 09:19:35'),
(13, 49, 4, '???', NULL, 1, '2017-08-10 09:19:48'),
(14, 49, 66, 'Hello, what about this job?', NULL, 1, '2017-08-17 05:40:43'),
(15, 49, 66, 'Hello, what about this job?', NULL, 0, '2017-08-17 05:41:12'),
(16, 49, 66, 'Hello, what about this job?', NULL, 0, '2017-08-17 05:41:14'),
(17, 66, 49, 'hello!', NULL, 1, '2017-08-29 15:09:13'),
(18, 66, 49, 'fghdfgh', NULL, 1, '2017-08-29 15:10:50');

-- --------------------------------------------------------

--
-- Структура таблицы `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL,
  `specification` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `category_id` int(11) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `post`
--

INSERT INTO `post` (`id`, `specification`, `title`, `price`, `category_id`, `latitude`, `longitude`, `status`, `user_id`, `date`) VALUES
(4, 'gdddddddddood post', 'Test title', 500, 2, 'laasdasdtitude', 'longadsasditude', 0, 1, NULL),
(5, 'test', 'dskfgsdfg', 22, 1, '123', '123312', 0, 4, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `post_image`
--

CREATE TABLE IF NOT EXISTS `post_image` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `post_image`
--

INSERT INTO `post_image` (`id`, `post_id`, `image`) VALUES
(13, 4, 'post_image/5964832fdaad3.jpg'),
(14, 4, 'post_image/5964832fdca81.jpg'),
(15, 4, 'post_image/5964832fdd4f8.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `push_notifications`
--

CREATE TABLE IF NOT EXISTS `push_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` int(11) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `push_notifications`
--

INSERT INTO `push_notifications` (`id`, `user_id`, `message`) VALUES
(2, 65, 0),
(3, 66, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `report`
--

CREATE TABLE IF NOT EXISTS `report` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `report`
--

INSERT INTO `report` (`id`, `sender_id`, `post_id`, `date`, `text`) VALUES
(1, 65, 4, '2017-08-21 08:07:21', 'Bad post!');

-- --------------------------------------------------------

--
-- Структура таблицы `token_devices`
--

CREATE TABLE IF NOT EXISTS `token_devices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_device` varchar(255) NOT NULL,
  `is_ios` int(11) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `token_devices`
--

INSERT INTO `token_devices` (`id`, `user_id`, `token_device`, `is_ios`) VALUES
(4, 66, '9d173d4d98720d9b650c083f5dec5628273b38cfd2e15a5e937581a8916ad147', 1),
(5, 66, '9d173d4d98720d9b650c083f5dec5628273b38cfd2e15a5e937581a8916ad154', 1),
(6, 66, '9d173d4d98720d9b650c083f5dec5628273b38cfd2e15a5e937581123a8916ad154', 1),
(7, 1, '9d173d4d98720d9b650c083f5dec5628273b38cfd2e15a5e937581a8916ad147', 1),
(8, 1, '123', 1),
(9, 1, '123123123', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `raiting` float DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `avatar`, `username`, `phone`, `country`, `city`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `raiting`, `created_at`, `updated_at`) VALUES
(1, 'No image', 'Admin2', '+380961349361', 'Ukraine', 'Dnepr', 'UTo8q7LX4oqzNWicwZE7txlyFy8QQlXf', '$2y$13$Am4uFBC2qwAbpFYeKMsGMOriVSwjP4e.ssnwdC8IREzJZZbSBbW1y', NULL, 'prybylov.v@gmail.com', 10, 2.5, 1490176869, 1504008234),
(4, NULL, 'vasya', NULL, NULL, NULL, 'UTo8q7LX4oqzNWicwZE7txFy8QQlXf', '$2y$13$jdKfU1vtbmHEY.P8Wg7W4.40CHL7BZU5yKbjnWsY0qtg2.a58V81S', NULL, 'vlad.vasyakot@mail.ru', 10, NULL, 1490185426, 1498065976),
(25, NULL, 'Ivan', '+380965789561', 'Ukraine', 'Dnepr', 'QMUgL-s73gweAoRId7DtaGox5RvnQM6M', '$2y$13$IvaNOxMlXmjR5R92b.JQpeJXTPQtajd6bTvZ871lbpwj5qEK3RVQu', NULL, 'gogo@gmail.com', 10, NULL, 1498927666, 1498927666),
(32, NULL, 'Test', '+380965789361', 'Ukraine', 'Dnepr', 'g9wWgZJYX7RfFb27IVjmMXITEm4_Bs71', '$2y$13$bQdW/E87rLC77JYo/nnTbuN6eM5K5t9T4ROizsCLwHB.2V99ZtXE2', NULL, 'test@gmail.com', 10, NULL, 1499068290, 1499068290),
(33, '123', 'Test22', '+380965709361', 'Ukraine', 'Dnepr', 'rfgwEHhhL26as-NSs-Du6C-zoljPdtWZ', '$2y$13$lUL9.MXlRlL3m72kSLJUhOzX6v3kwzUuM9B0nU5en4SQHNpEl6oNO', NULL, 'test13@gmail.com', 10, NULL, 1499069761, 1499163936),
(39, 'avatars/595cc61359cdb.png', 'hhhh', '+380665709361', 'Ukraine', 'Dnepr', 'DItENCEhn1PbgZOugFgSRQCPAdxvMraF', '$2y$13$yk6IC1ZIM0kAi8BCKdJMPuPFbvVDMr2ZZI0P2at9o62Y/wBvS011C', NULL, 'alalal@gmail.com', 10, NULL, 1499170554, 1499252243),
(48, 'No image', 'Vas1ya', '+380965509361', 'Ukraine', 'Dnepr', 'xt1ZdqnHIi2eCQpvcy91e2HgC34sxe1Z', '$2y$13$I5zo.7rM.bSB.f2/IRWptepfF5ND/T/HzHerth0Nx7gl.Blo4IjXW', NULL, 't@gmail.com', 10, NULL, 1499259965, 1499259965),
(49, 'https://media.licdn.com/mpr/mprx/0_0gzKp967bYU6a6tbJudBsFUEkaVw2Q-kp0d12eV79lVmgExkmNdnfhQdNaVI2o1FeuHBIYFdbIRIxBtas9k0IDbfrIRwxBmFe9ktY9U75AECxh0metNPNdvxUm', 'Влад Прибылов', '+380678954670', 'Ukraine', 'Dnepr', 'RTPqDS1ROSl4mEL-Rdn1AMHv-wwck8SO', '$2y$13$ffIT4ujZrFBfjO.WGKSowue6d8.EVcaAh28SqGKTvC6Ir4uguOHXq', NULL, 'prybylov.v2@gmail.com', 10, NULL, 1499667691, 1499667691),
(65, 'No image', 'Vasya', '', 'Ukraine', 'Dnepr', '', '$2y$13$Jtknp92IxHk4pwzitSTW8eXZCJ8cQybWDT6C1zaTsVpOgopK.dQxC', NULL, 'test123@gmail.com(deleted)', 0, NULL, 1501834382, 1503304090),
(66, 'No image', 'Vasya123', '+380965989361', 'Ukraine', 'Dnepr', 'qAV3RSbRBVlEE2LOt-784bGaE4KLu81W', '$2y$13$sH0sWyZuYhbQWGJAwIvsWuTmzgIJ/XhnH9dV9BTC0C8PNBXIq.gwi', NULL, 'test_vasya@gmail.com', 10, NULL, 1502704190, 1502706325);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `favorites_ibfk_2` (`user_id`),
  ADD KEY `favorites_ibfk_1` (`post_id`);

--
-- Индексы таблицы `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_ibfk_2` (`recepient_id`),
  ADD KEY `message_ibfk_1` (`sender_id`);

--
-- Индексы таблицы `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_ibfk_1` (`user_id`),
  ADD KEY `post_ibfk_2` (`category_id`);

--
-- Индексы таблицы `post_image`
--
ALTER TABLE `post_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_image_ibfk_1` (`post_id`);

--
-- Индексы таблицы `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `push_notifications_ibfk_1` (`user_id`);

--
-- Индексы таблицы `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Индексы таблицы `token_devices`
--
ALTER TABLE `token_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token_devices_ibfk_1` (`user_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT для таблицы `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `post_image`
--
ALTER TABLE `post_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT для таблицы `push_notifications`
--
ALTER TABLE `push_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `token_devices`
--
ALTER TABLE `token_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=67;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`recepient_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `post_image`
--
ALTER TABLE `post_image`
  ADD CONSTRAINT `post_image_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD CONSTRAINT `push_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`);

--
-- Ограничения внешнего ключа таблицы `token_devices`
--
ALTER TABLE `token_devices`
  ADD CONSTRAINT `token_devices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
