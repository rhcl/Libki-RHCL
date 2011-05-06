SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Data for table `logins`
--

INSERT INTO `logins` (`username`, `password`, `units`, `status`, `message`, `notes`, `last_accessed`, `machine`, `admin`) VALUES
('admin', 'UFEi6S6DI8BF1L0qA7giwQ', 30, 'Logged out', NULL, NULL, '2009-02-05 13:33:45', '', 1);

--
-- Data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `description`) VALUES
(1, 'version', '0.200002', 'Current Version of the LibKi Database'),
(2, 'max_pause_time', '10', 'The amount of time ( in minutes ) before a paused kiosk will revert to a normal state where anyone can log in.'),
(3, 'time_before_auto_logout', NULL, 'The length of inactivity ( in minutes ) before a kiosk will automatically log out.'),
(4, 'post_crash_timeout', '2', 'This is the length of time ( in minutes ) before a logged in user whose kiosk crashed can log back in.'),
(5, 'seconds_between_client_updates', '10', 'This is the length of time ( in seconds ) between client updates. Short is more responsive, longer means less network traffic.'),
(6, 'machine_name_filters', 'Fiction->fic::Main Floor->patron::Childrens->juv', 'If you have a standard naming convention for kiosks, putting the standard prefixes in here will allow you to filter machines by floor. Example data: "main::childrens::fiction"'),
(7, 'next_guest_id', '1', 'The next number to use for the guest id sequence. e.g. guest1, guest2, etc..'),
(8, 'daily_minutes', '30', 'The number of minutes each user will start with each day.'),
(9, 'use_koha_integration', '0', 'Turn on Koha integration with Libki'),
(10, 'koha_intranet_url', '', 'The address of your Koha intranet server.');

