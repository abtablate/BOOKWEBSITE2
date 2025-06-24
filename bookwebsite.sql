-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 08:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookwebsite`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `cover` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `rating` decimal(3,1) DEFAULT 0.0,
  `rating_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `cover`, `description`, `available`, `rating`, `rating_count`) VALUES
(1, 'Near, Far, Wherever You Are', 'Mary  (AI Generated)', 'uploads/covers/6851b26d19e50.jfif', 'Genre: Romance, Drama, Contemporary\r\n\r\nAvery Brooks, a young writer from New York, never believed in fate—until she met Kai Morales, a photographer from the Philippines, during a study program in Rome. They fell in love fast and deep, spending only 17 days together before being torn apart by distance, time zones, and life’s uncertainties.\r\n\r\nNow, a year later, Avery receives a letter. No name. No return address. Just one line written on the envelope:\r\n\r\n“Near, far, wherever you are.”\r\n\r\nAnd just like that, the journey to find him again begins—with nothing but their memories and the promise they once made under a Roman sunset.', 1, 0.0, 0),
(3, 'The Girl with the Red Lipstick', 'Lisa (AI Generated)', 'uploads/covers/6851b3543fa16.jfif', 'Genre: Mystery, Romance, Drama\r\n\r\nIn a sleepy coastal town, 18-year-old Elias Raines lives a quiet, predictable life — until he sees her.\r\n\r\nShe’s standing by the boardwalk in the rain, wearing a black coat and red lipstick that cuts through the fog like a warning. She’s beautiful. Strange. Untouchable. And gone before he can even ask her name.\r\n\r\nBut when Elias begins seeing her again — always at night, always alone — he becomes obsessed with finding out who she is. What he discovers is a secret that changes everything: she doesn’t exist in any records. No school ID. No phone number. Nothing.\r\n\r\nIs she real, or just a dream?\r\n\r\n', 1, 0.0, 0),
(4, 'Boy next door', 'krissa (AI Generated)', 'uploads/covers/6851b49f4b01c.jfif', ' Genre: Romance, Coming-of-Age, Drama, Light Comedy\r\n\r\nWhen 17-year-old Aubrey Lane moves to a quiet town with her mother after her parents’ separation, she doesn’t expect anything exciting to happen. That is, until she meets Liam Rivera, the mysterious, charming boy who lives next door.\r\n\r\nLiam is everything Aubrey isn’t: confident, outgoing, and hiding something. As their worlds begin to intertwine, secrets are uncovered, hearts get tangled, and both teens must face the truth about love, loss, and what it really means to grow up.\r\n\r\nWhat happens when your biggest comfort is also your biggest confusion?', 1, 0.0, 0),
(5, 'Ika anim na utos', 'Mary Jasmine (AI Generated)', 'uploads/covers/6851b591d4927.png', 'Genre: Dramang Pampamilya, Pag-ibig, Paghihiganti\r\n\r\n\r\nAng \"Ika-Anim na Utos\" ay umiikot sa buhay ni Clarisse, isang babaeng nagmahal, nagtiwala, at nasaktan nang labis. Sa loob ng pitong taong kasal nila ni Rommel, naniwala siyang matatag ang kanilang pagsasama. Ngunit biglang nagbago ang lahat nang malaman niyang may ibang babae ang asawa niya — si Emma, ang dating matalik niyang kaibigan.\r\n\r\nSa bawat pagtataksil, unti-unting napupuno ng galit ang kanyang puso. Ngunit sa kabila ng sakit, kailangan niyang tumindig — para sa kanyang anak, sa kanyang dangal, at sa kanyang sarili. Sa kanyang paglalakbay, matutuklasan niya ang tunay na kahulugan ng ikaanim na utos: “Huwag kang mangangalunya.”', 1, 0.0, 0),
(6, 'Solo Levelling', 'Sung-Lak Jang', 'solo levelling.jpg', 'Status : Ongoing\r\n\r\nGenres : Action , Adventure , Fantasy , Shounen , Webtoons\r\n\r\nI am the only the one who levels up, I level up alone, Na Honjaman Lebel-eob, Only I Level Up, Ore Dake Level Up na Ken, Поднятие уровня в одиночку, 나 혼자만 레벨업, 俺だけレベルアップな件, 我独自升级', 1, 0.0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `chapter_number` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image` int(11) NOT NULL,
  `chapter_type` enum('text','image') NOT NULL DEFAULT 'text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `book_id`, `chapter_number`, `title`, `content`, `image`, `chapter_type`) VALUES
(9, 6, 1, 'chapter 0 PROLOGUE', 'uploads/chapters/6850b1e4e91c4.jpg\nuploads/chapters/6850b1e4e953a.jpg\nuploads/chapters/6850b1e4e97d7.jpg\nuploads/chapters/6850b1e4e9a42.jpg\nuploads/chapters/6850b1e4e9d0d.jpg\nuploads/chapters/6850b1e4e9fed.jpg\nuploads/chapters/6850b1e4ea261.jpg\nuploads/chapters/6850b1e4ea4b0.jpg', 0, 'image'),
(11, 1, 1, 'Chapter 1: Seventeen Days in Rome', 'Avery hadn’t planned to fall in love when she arrived in Rome for her summer writing course. But then she met Kai—funny, thoughtful, with a camera always around his neck and a quiet sadness in his eyes.\r\n\r\nThey shared gelato, stories, and midnight walks by the Tiber River. Every moment felt eternal, even though they both knew the clock was ticking.\r\n\r\nOn their last night, they sat on the Spanish Steps and made a promise:\r\n\r\n“No matter what happens, near or far—wherever you are, remember me.”', 0, 'text'),
(12, 1, 2, 'Chapter 2: Silence After Sunrise', 'Back in New York, Avery tried to hold onto Kai through messages, video calls, and emails. But time zones became excuses. Work, school, pressure. Slowly, the messages faded.\r\n\r\nThen… silence.\r\n\r\nNo explanation. No goodbye.\r\n\r\nShe wrote dozens of unsent letters to him. Her stories were full of boys with cameras and girls who waited. But she never moved on.\r\n\r\nUntil, one year later, she received a handwritten letter with no name—just one haunting line:\r\n\r\n“Near, far, wherever you are.”\r\n\r\nAnd inside… a photo of her. Laughing, taken from behind.', 0, 'text'),
(13, 1, 3, 'Chapter 3: The Letter Trail', 'Avery followed the return stamp to a small postal office in San Francisco. The clerk didn’t know the sender but remembered the handwriting. “He said he was passing through. Filipino, I think.”\r\n\r\nShe took a leave from work and booked a flight to San Francisco.\r\n\r\nThere, she visited galleries, photography meet-ups, and even the bay bridges—any place a photographer might go.\r\n\r\nThen she found another envelope taped to a lamppost near Chinatown. Her name. No note. Just a single photo: a sunset over the Golden Gate Bridge.\r\n\r\nHe was leaving her clues.', 0, 'text'),
(14, 1, 4, 'Chapter 4: Across the Ocean', 'The trail ended at a small exhibit called “The Girl Who Waited.”\r\n\r\nPhotographs of a woman—her. Laughing, walking, writing. All candid. All taken in Rome.\r\n\r\nA note at the end read:\r\n\r\n“If she finds this, meet me where we first promised forever.”\r\n\r\nAvery flew to Rome.\r\n\r\nJetlagged, breathless, and unsure if he’d really be there, she climbed the Spanish Steps at sunset.\r\n\r\nAnd there he was.\r\n\r\nCamera slung across his shoulder. Same soft eyes.\r\n\r\n“I hoped you\'d come,” he said.\r\n\r\n', 0, 'text'),
(15, 1, 5, 'Chapter 5: Wherever We Are', 'They sat side by side, as they once had, and said nothing for a while. Just breathing. Just existing.\r\n\r\n“I was scared I wasn’t enough for your future,” Kai finally said. “But I never stopped trying to reach you.”\r\n\r\n“And I never stopped waiting,” Avery replied. “Even when I pretended I had.”\r\n\r\nHe pulled out a letter—the one he never sent.\r\n\r\nInside, it said:\r\n\r\n“You’ll always be my favorite chapter. Whether I’m near… or far.”\r\n\r\nThis time, there were no promises. Just presence.\r\n\r\nWherever they would be next, they\'d carry this moment with them.', 0, 'text'),
(16, 3, 1, 'Chapter 1: The First Glimpse', 'Elias is biking home from his shift at the café when he sees her — standing at the edge of the pier, looking out at the ocean. Her red lipstick glows under the streetlight.\r\n\r\nHe stops. She turns. Smiles.\r\n\r\nThen she’s gone.', 0, 'text'),
(17, 3, 2, 'Chapter 2: Name Unknown', 'At school the next day, Elias asks around — no one knows her. Not the seniors, not the teachers. He starts sketching her in his notebook, unable to forget that face.\r\n\r\nHis friend Juno calls her “The Phantom Girl.” But Elias swears she’s real.\r\n\r\n', 0, 'text'),
(18, 3, 3, 'Chapter 3: Stranger in the Library', 'Elias visits the old town library and is stunned to find her reading alone in the poetry section. This time, he speaks.\r\n\r\n“Hi.”\r\n\r\nShe looks up. “It’s about time.”\r\n\r\nHer name? Ivy. But she won’t say her last name.', 0, 'text'),
(19, 3, 4, 'Chapter 4: Red Lipstick and a Warning', 'Elias and Ivy meet more often, always in odd places: the train station, the old garden behind the museum, the cemetery.\r\n\r\nEvery time, she’s wearing that same shade of red. One night she whispers:\r\n\r\n“Don’t fall in love with me, Elias. You’ll regret it.”', 0, 'text'),
(20, 3, 5, 'Chapter 5: The Dream Diary', 'Elias begins having vivid dreams of Ivy — but in a different time. Old buildings. Horses. No phones. He wakes up with drawings on his hands and poems in his head that he swears he didn’t write.', 0, 'text'),
(23, 3, 6, 'Chapter 6: The Red Lipstick Clue ', 'It was a cold Thursday evening when Elias saw Ivy again. She stood at the far end of the train platform, facing the tracks, unmoving. Her black coat danced with the wind, and the red lipstick still burned like a mark against her pale skin.\r\n\r\nElias approached carefully. But before he could say a word, Ivy turned, placed something gently on the bench beside her, and walked away into the fog. She didn’t say goodbye. She didn’t look back.\r\n\r\nElias rushed to the bench. Sitting there was the familiar red lipstick tube. But when he opened it, it wasn’t lipstick inside — it was a tiny, folded photograph tucked into a hollowed center.\r\n\r\nHands trembling, he opened the photo.\r\nIt was old. Worn. Black and white.\r\nA girl — Ivy — standing beside a young man in a tuxedo, smiling under fairy lights. The caption scribbled in fading ink said:\r\n\r\n\"Prom Night — June 1958.\"\r\n\r\nBut that couldn’t be.\r\n\r\nIvy looked exactly the same. Same eyes. Same red lips. Same sadness behind the smile.\r\n\r\nElias sat down hard, heart pounding. Either this was a prank, or the girl he thought he was falling for was somehow not from this world.', 0, 'text'),
(24, 3, 7, ' Chapter 7: The Letter', 'Unable to sleep, Elias showed the photo to Juno the next day. She gasped.\r\n\r\n“I know this place,” she said, pointing to the background. “That’s the old hotel on Cherry Street. My grandma said it used to host town dances.”\r\n\r\nThey spent hours researching in the town archives. Finally, they found it: a newspaper clipping dated June 17, 1958.\r\n\r\n\"MYSTERY AT PROM NIGHT\r\nLocal girl Ivy Astor, 18, vanished from Lakeside Hotel during senior prom. Witnesses last saw her leaving early, wearing a black dress and bright red lipstick. She never made it home. Case remains unsolved.\"*\r\n\r\nElias couldn’t breathe. Ivy Astor. The name she never told him.\r\n\r\nThat night, a letter was slipped under his bedroom door. Handwritten. No stamp.\r\n\r\n“Elias,\r\nI wish I could explain everything, but I can’t — not yet.\r\nThe past is a loop I haven’t escaped. You were never meant to find me, but I stayed too long.\r\nDon’t look for me.\r\nAnd whatever happens, please remember me as I am now.\r\n– Ivy”\r\n\r\nHe read it over and over, clutching it to his chest like a lifeline. The girl with the red lipstick wasn’t just a dream — she was a ghost from a forgotten time.\r\n\r\nAnd he was in love with her.', 0, 'text'),
(25, 3, 8, ' Chapter 8: Kiss Before Midnight ', 'New Year’s Eve arrived with bitter wind and brilliant stars. The entire town gathered at the town square for fireworks and dancing. But Elias slipped away, heart pounding, clutching the red lipstick like a talisman.\r\n\r\nHe found Ivy waiting by the ocean pier, her breath fogging in the cold air, her black coat fluttering behind her.\r\n\r\n“I shouldn’t have come,” she said softly.\r\n\r\n“I had to see you,” Elias replied. “Even if it’s for the last time.”\r\n\r\nShe walked to him, touched his face like it might vanish. “You weren’t supposed to remember me.”\r\n\r\n“But I do,” he whispered. “And I don’t want to forget.”\r\n\r\nAs the countdown began in the distance — ten, nine, eight — Ivy leaned in. Her lips brushed his. Fireworks exploded behind them. The kiss was warm, aching, and final.\r\n\r\n“I love you,” he said against her forehead.\r\n\r\nShe closed her eyes. “Then I’m sorry.”\r\n\r\nWhen he opened his eyes again, she was stepping backward into the fog, her figure disappearing like smoke.', 0, 'text'),
(26, 3, 9, 'Chapter 9: Gone with the Rain', 'The days that followed were heavy with silence. Elias searched every place they’d ever been — the garden, the cemetery, the old hotel. No sign of Ivy. Not a whisper. Not a trace.\r\n\r\nJuno tried to comfort him, but Elias couldn’t shake the feeling that Ivy hadn’t just left — she had returned to a time she could no longer escape.\r\n\r\nRain came in sheets one afternoon as he visited the pier. There, right where she had last stood, he found the empty lipstick tube. No photo. No note. Just that familiar scent of roses and something... older.\r\n\r\nHe took it home and placed it in a small glass box on his shelf.\r\n\r\nShe was gone.\r\n\r\nBut she had been real.\r\n\r\nAnd she had mattered', 0, 'text'),
(27, 3, 10, 'Chapter 10: The Girl in the Portrait', 'Years passed.\r\n\r\nElias moved to the city, studied art, and became a painter known for one particular portrait — a girl with sharp eyes, dark hair, and unforgettable red lips.\r\n\r\nHis gallery always held the same question from strangers:\r\n“Who is she?”\r\n\r\nSome said she was a dream. Others thought she was a symbol of heartbreak.\r\n\r\nBut to Elias, she was everything.\r\n\r\nHe never told the full story, only ever replying:\r\n\r\n“She was the girl who taught me that love isn’t always about forever.\r\nSometimes, it’s about remembering.”\r\n\r\nIn his quiet studio, the red lipstick still sat in its glass case.\r\n\r\nWaiting.\r\n\r\nJust in case…\r\nShe ever came back.', 0, 'text'),
(28, 4, 1, ' Chapter 1: The Move-In', 'It was the hottest day of June when Aubrey and her mom arrived at their new house in Fairhill. The paint on the porch was chipping, and the place smelled like dust and old wood. But her mom called it a “fresh start.”\r\n\r\nAs she carried boxes from the car, Aubrey noticed someone across the fence. A boy. Dark hair, gray hoodie despite the heat, and headphones in. He glanced at her and gave the smallest nod.\r\n\r\nShe nodded back.\r\nThe boy next door didn’t say a word. But something in his eyes said they’d meet again.', 0, 'text'),
(29, 4, 2, ' Chapter 2: Lemonade and Secrets', 'Three days later, Aubrey’s mom forced her to deliver homemade lemonade to the neighbors.\r\n\r\nWhen she reached the Rivera house, it was the boy who answered the door.\r\n\r\n“You again,” he said, smirking.\r\n\r\n“You never said hi the first time,” she shot back.\r\n\r\nHe took the glass. “Liam.”\r\n\r\n“Aubrey.”\r\n\r\nAn awkward silence followed.\r\n\r\nAnd then — “Wanna see something cool?”\r\n\r\nIn that moment, Aubrey stepped into the house — and unknowingly into Liam’s secret world.\r\n\r\n', 0, 'text'),
(30, 4, 3, 'Chapter 3: His Room', 'Liam’s room was full of records, Polaroids, and drawings on the walls. It smelled like mint and cologne.\r\n\r\n“You draw?” Aubrey asked.\r\n\r\n“Sometimes,” he said. “Mostly things I can’t say.”\r\n\r\nHe handed her a sketch of a boy standing alone under the rain.\r\n\r\n“That’s you?”\r\n\r\n“Maybe.”\r\n\r\nIt was the first time Aubrey realized — Liam wasn’t just a boy with a smirk. He was layered like the songs in his playlist: loud on the outside, but soft underneath.\r\n\r\n', 0, 'text'),
(31, 4, 4, 'Chapter 4: Fireflies', 'That weekend, Liam showed up outside her window.\r\n\r\n“Come on. No questions.”\r\n\r\nHe led her to the field behind their houses. Fireflies lit the air like stars trapped in a jar.\r\n\r\nThey lay down on the grass, staring at the sky.\r\n\r\n“Why are you being nice to me?” she asked.\r\n\r\n“Because you looked sad the first day.”\r\n\r\nShe looked at him. “And you?”\r\n\r\n“I’ve been sad a lot longer.”\r\n\r\nIn the silence that followed, their hands touched — and neither pulled away.', 0, 'text'),
(32, 4, 5, 'Chapter 5: Letters in the Box', 'One stormy night, Aubrey’s mom told her a secret: the Rivera family lost a son two years ago. His name was Lucas.\r\n\r\nLiam’s older brother.\r\n\r\nConfused and shaken, Aubrey ran to the old mailbox they used as a joke to trade notes.\r\n\r\nInside, a letter waited for her.\r\n\r\n\"Aubrey — if I seem distant sometimes, it’s because I’m afraid of losing people again. But you… you make the silence quieter.\" – L.\"\r\n\r\nHer heart raced.\r\n\r\nShe now knew two things:\r\nLiam had lost someone.\r\nAnd he didn’t want to lose her too.', 0, 'text'),
(33, 5, 1, ' Kabanata 1: Ang Perpektong Asawa', 'Tahimik ang umaga. Habang si Clarisse ay abalang naghahanda ng almusal, nakaupo si Rommel sa sala, tutok sa balita. Isang pangkaraniwang eksena ng isang pamilyang buo — o iyon ang akala niya.\r\n\r\nPagkaalis ni Rommel para pumasok sa opisina, umupo si Clarisse sa harap ng kanyang laptop upang ituloy ang pagsusulat sa kanyang parenting blog. Bigla siyang nakatanggap ng email mula sa hindi kilalang sender:\r\n“Alamin mo kung sino ang kasama ni Rommel tuwing gabi. Baka hindi na siya sayo lang umuuwi.”\r\n\r\nNapakunot-noo siya. Inisip niyang baka prank lang. Pinili niyang magtiwala, sapagkat si Rommel ay isang responsableng ama at asawa. Ngunit sa likod ng kanyang mga ngiti, may itinatago pala siyang sugat na malapit nang mabuksan.', 0, 'text'),
(34, 5, 2, 'Kabanata 2: Larawan ng Kataksilan', 'Linggo ng hapon. Nagsusuklay si Clarisse sa harap ng salamin habang nag-aayos ng aparador. Napansin niyang may envelope na nakasingit sa bulsa ng blazer ni Rommel. Sa kanyang pag-uusisa, nahulog ang envelope at bumukas — bumungad sa kanya ang ilang litratong nagpapakita ng hindi maipagkakailang pagtataksil.\r\n\r\nSa mga larawan, magkahawak-kamay sina Rommel at isang pamilyar na babae — si Emma. Magkaakbay sa isang beach. Nagkikiss sa isang hotel room.\r\n\r\nTumigil ang mundo ni Clarisse. Hindi makagalaw. Hindi makaiyak. Sa isang iglap, gumuho ang lahat ng paniniwala niyang siya lang ang mahal ng asawa niya.\r\n\r\nSa gitna ng pananakit, may kapangyarihan siyang natuklasan: ang lakas na mula sa isang inang pinili nang lumaban. Hindi na siya ang dating Clarisse.', 0, 'text');

-- --------------------------------------------------------

--
-- Table structure for table `continue_reading`
--

CREATE TABLE `continue_reading` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `last_accessed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `most_read`
--

CREATE TABLE `most_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `read_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recently_read`
--

CREATE TABLE `recently_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recently_read`
--

INSERT INTO `recently_read` (`id`, `user_id`, `book_id`, `read_at`) VALUES
(7, 6, 3, '2025-06-16 15:36:07'),
(12, 6, 1, '2025-06-16 16:20:40'),
(36, 6, 5, '2025-06-17 09:37:21'),
(37, 6, 6, '2025-06-17 09:38:01'),
(41, 8, 1, '2025-06-17 18:34:21'),
(42, 7, 1, '2025-06-17 18:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `usertag` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `username` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default_profile.jpg',
  `display_name` varchar(100) DEFAULT NULL,
  `bio` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `username`, `profile_pic`, `display_name`, `bio`) VALUES
(2, 'glenn@gmail.com', '$2y$10$2BP3lD.15ZDRTlMCE.cb4eujhZWVUTOyIrJRMsA/nB0m1lmI/f6Q2', 'user', NULL, 'default_profile.jpg', NULL, NULL),
(3, 'mint@gmail.com', '$2y$10$5D7DF6yVVhPQmPDng.XqBe2NbQU4GwJfKJ3HSeGwK1O1FOspxVAEm', 'user', NULL, 'default_profile.jpg', NULL, NULL),
(4, 'leahmarieperina7@gmail.com', '$2y$10$EwruBQ1yILtGsjytjNFJOux2gFjqlRyZSYv65tdNBbqoVmaAsiS8C', 'user', NULL, 'default_profile.jpg', NULL, NULL),
(5, 'floyd05@gmail.com', '$2y$10$Kktv3NXntCUEQK.7Wh9IeuuoZIIKLf9Le.dwq2.SUoiqwYlg4Nz6y', 'user', NULL, 'default_profile.jpg', NULL, NULL),
(6, '123456@gmail.com', '$2y$10$USUIEPSWrCZFT.k/xpMMye0IHUgHz0PDd2v.ezoNSZeSTiyI2IKd2', 'admin', 'MANALO MATALO', 'uploads/profile_pics/user_6_1750154532.jpg', 'MATALO', 'LUTANG PALAGI'),
(7, 'User@gmail.com', '$2y$10$6386X1W2UkWv5T15CkZP9.lc6qxp2jccqctVeJUks0EdcYD1MTX3i', 'user', 'Mary', 'uploads/profile_pics/user_7_1750185500.jfif', 'Jasmine', 'Book Lover'),
(8, 'admin@gmail.com', '$2y$10$Xfm/42.AR7TtDM1WFpjH0.eswo/jbQJDWBnyv.ADSB4kJrtb.H1ie', 'admin', NULL, 'default_profile.jpg', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `continue_reading`
--
ALTER TABLE `continue_reading`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_continue` (`user_id`,`book_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `most_read`
--
ALTER TABLE `most_read`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `recently_read`
--
ALTER TABLE `recently_read`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `continue_reading`
--
ALTER TABLE `continue_reading`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `most_read`
--
ALTER TABLE `most_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recently_read`
--
ALTER TABLE `recently_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
