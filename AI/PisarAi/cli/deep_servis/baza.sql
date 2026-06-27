-- Tabela za AI zgodovino
CREATE TABLE IF NOT EXISTS `ai_zgodovina` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `session_id` varchar(64) NOT NULL,
    `agent_id` varchar(50) NOT NULL,
    `api_key_id` int(11) DEFAULT NULL,
    `vloga` enum('user','assistant','system') NOT NULL,
    `vsebina` text NOT NULL,
    `odgovor_na` int(11) DEFAULT NULL,
    `model` varchar(50) DEFAULT 'deepseek-chat',
    `tokens_uporabljeni` int(11) DEFAULT 0,
    `cas` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `session_id` (`session_id`),
    KEY `agent_id` (`agent_id`),
    KEY `cas` (`cas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela za API ključe
CREATE TABLE IF NOT EXISTS `ai_api_kljuci` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ime` varchar(100) NOT NULL,
    `api_key` varchar(255) NOT NULL,
    `bazni_url` varchar(255) DEFAULT 'https://api.deepseek.com',
    `model` varchar(50) DEFAULT 'deepseek-chat',
    `aktivno` tinyint(1) DEFAULT 1,
    `max_dnevno` int(11) DEFAULT 1000,
    `porabljeno_danes` int(11) DEFAULT 0,
    `zadnja_uporaba` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela za agent-e
CREATE TABLE IF NOT EXISTS `ai_agenti` (
    `id` varchar(50) NOT NULL,
    `ime` varchar(100) NOT NULL,
    `opis` text,
    `system_prompt` text,
    `default_api_key_id` int(11) DEFAULT NULL,
    `temperatura` float DEFAULT 0.7,
    `max_tokens` int(11) DEFAULT 2000,
    `aktivno` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vstavi osnovne agente
INSERT INTO `ai_agenti` (`id`, `ime`, `opis`, `system_prompt`, `temperatura`, `max_tokens`) VALUES
('bloger', '📝 Blog pisec', 'Specializiran za pisanje blog člankov', 'Ti si profesionalni blog pisec. Pišeš privlačne, informativne in SEO-optimizirane članke v slovenskem jeziku.', 0.8, 2500),
('analitik', '🔍 Analitik', 'Analizira vsebino in podatke', 'Ti si analitik. Analiziraš podano vsebino, iščeš vzorce, povzemaš in podaš priporočila.', 0.5, 1500),
('kreativec', '🎨 Kreativec', 'Ustvarjalno pisanje in ideje', 'Ti si kreativni pisec. Ustvarjaš zanimive zgodbe, metafore in kreativne vsebine.', 0.9, 2000),
('urejevalec', '✏️ Urejevalec', 'Lektorira in izboljšuje besedila', 'Ti si profesionalni lektor. Popravljaš slovnico, slog in berljivost besedil.', 0.3, 1000);

-- Vstavi primer API ključa (zamenjaj z pravim)
INSERT INTO `ai_api_kljuci` (`ime`, `api_key`, `model`) VALUES 
('DeepSeek Glavni', 'tvoj_deepseek_api_key_1', 'deepseek-chat'),
('DeepSeek Rezervni', 'tvoj_deepseek_api_key_2', 'deepseek-chat');