-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июл 10 2017 г., 11:43
-- Версия сервера: 5.6.31
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
-- Структура таблицы `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `post`
--

INSERT INTO `post` (`id`, `name`, `title`, `latitude`, `longitude`, `status`, `user_id`) VALUES
(1, 'New Post', 'new title', 'latitude', 'longitude', 0, 49),
(2, 'New Post', 'new title', 'latitude', 'longitude', 0, 49),
(3, 'New Post', 'new title', 'latitude', 'longitude', 0, 49);

-- --------------------------------------------------------

--
-- Структура таблицы `post_image`
--

CREATE TABLE IF NOT EXISTS `post_image` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

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
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `avatar`, `username`, `phone`, `country`, `city`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Admin', 'ghj', 'fghj', 'fghj', 'UTo8q7LX4oqzNWicwZE7txlyFy8QQlXf', '$2y$13$jdKfU1vtbmHEY.P8Wg7W4.40CHL7BZU5yKbjnWsY0qtg2.a58V81S', NULL, 'prybylo2.v@gmail.com', 10, 1490176869, 1490179685),
(4, NULL, 'vasya', NULL, NULL, NULL, 'UTo8q7LX4oqzNWicwZE7txFy8QQlXf', '$2y$13$jdKfU1vtbmHEY.P8Wg7W4.40CHL7BZU5yKbjnWsY0qtg2.a58V81S', NULL, 'vlad.vasyakot@mail.ru', 10, 1490185426, 1498065976),
(25, NULL, 'Ivan', '+380965789561', 'Ukraine', 'Dnepr', 'QMUgL-s73gweAoRId7DtaGox5RvnQM6M', '$2y$13$IvaNOxMlXmjR5R92b.JQpeJXTPQtajd6bTvZ871lbpwj5qEK3RVQu', NULL, 'gogo@gmail.com', 10, 1498927666, 1498927666),
(32, NULL, 'Test', '+380965789361', 'Ukraine', 'Dnepr', 'g9wWgZJYX7RfFb27IVjmMXITEm4_Bs71', '$2y$13$bQdW/E87rLC77JYo/nnTbuN6eM5K5t9T4ROizsCLwHB.2V99ZtXE2', NULL, 'test@gmail.com', 10, 1499068290, 1499068290),
(33, '123', 'Test22', '+380965709361', 'Ukraine', 'Dnepr', 'rfgwEHhhL26as-NSs-Du6C-zoljPdtWZ', '$2y$13$lUL9.MXlRlL3m72kSLJUhOzX6v3kwzUuM9B0nU5en4SQHNpEl6oNO', NULL, 'test13@gmail.com', 10, 1499069761, 1499163936),
(39, 'avatars/595cc61359cdb.png', 'hhhh', '+380665709361', 'Ukraine', 'Dnepr', 'DItENCEhn1PbgZOugFgSRQCPAdxvMraF', '$2y$13$yk6IC1ZIM0kAi8BCKdJMPuPFbvVDMr2ZZI0P2at9o62Y/wBvS011C', NULL, 'alalal@gmail.com', 10, 1499170554, 1499252243),
(48, 'No image', 'Vas1ya', '+380965509361', 'Ukraine', 'Dnepr', 'xt1ZdqnHIi2eCQpvcy91e2HgC34sxe1Z', '$2y$13$I5zo.7rM.bSB.f2/IRWptepfF5ND/T/HzHerth0Nx7gl.Blo4IjXW', NULL, 't@gmail.com', 10, 1499259965, 1499259965),
(49, 'https://media.licdn.com/mpr/mprx/0_0gzKp967bYU6a6tbJudBsFUEkaVw2Q-kp0d12eV79lVmgExkmNdnfhQdNaVI2o1FeuHBIYFdbIRIxBtas9k0IDbfrIRwxBmFe9ktY9U75AECxh0metNPNdvxUm', 'Влад Прибылов', NULL, NULL, NULL, 'RTPqDS1ROSl4mEL-Rdn1AMHv-wwck8SO', '$2y$13$ffIT4ujZrFBfjO.WGKSowue6d8.EVcaAh28SqGKTvC6Ir4uguOHXq', NULL, 'prybylov.v@gmail.com', 10, 1499667691, 1499667691);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_ibfk_1` (`user_id`);

--
-- Индексы таблицы `post_image`
--
ALTER TABLE `post_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_image_ibfk_1` (`post_id`);

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
-- AUTO_INCREMENT для таблицы `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `post_image`
--
ALTER TABLE `post_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=50;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `post_image`
--
ALTER TABLE `post_image`
  ADD CONSTRAINT `post_image_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
