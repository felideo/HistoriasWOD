DROP TABLE IF EXISTS `modulo`;
DROP TABLE IF EXISTS `hierarquia`;
DROP TABLE IF EXISTS `arquivo`;
DROP TABLE IF EXISTS `ordem_usuario_menu`;
DROP TABLE IF EXISTS `banner`;
DROP TABLE IF EXISTS `pagina_institucional`;
DROP TABLE IF EXISTS `hierarquia_relaciona_permissao`;
DROP TABLE IF EXISTS `permissao`;
DROP TABLE IF EXISTS `configuracao`;
DROP TABLE IF EXISTS `pessoa`;
DROP TABLE IF EXISTS `plataforma`;
DROP TABLE IF EXISTS `plataforma_pagina`;
DROP TABLE IF EXISTS `submenu`;
DROP TABLE IF EXISTS `url`;
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `arquivo` ( `id` int(11) NOT NULL AUTO_INCREMENT, `hash` varchar(32) NOT NULL, `nome` varchar(128) NOT NULL, `endereco` varchar(256) NOT NULL, `tamanho` decimal(10,0) NOT NULL, `extensao` varchar(16) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `banner` ( `id` int(11) NOT NULL AUTO_INCREMENT, `ordem` int(11) NOT NULL, `id_arquivo` int(11) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`), KEY `id_arquivo` (`id_arquivo`), CONSTRAINT `banner_ibfk_1` FOREIGN KEY (`id_arquivo`) REFERENCES `arquivo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `configuracao` ( `id` int(11) NOT NULL AUTO_INCREMENT, `aplicacao_nome` varchar(32) DEFAULT NULL, `email` varchar(256) DEFAULT NULL, `id_google_analytics` varchar(128) DEFAULT NULL, `texto_direito_rodape` text, `texto_esquerdo_rodape` text, `cor_padrao` varchar(64) DEFAULT NULL, `politica_aprovacao` tinyint(1) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE `hierarquia` ( `id` int(11) NOT NULL AUTO_INCREMENT, `nome` varchar(64) NOT NULL, `nivel` int(11) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
CREATE TABLE `hierarquia_relaciona_permissao` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_hierarquia` int(11) NOT NULL, `id_permissao` int(11) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`), KEY `id_hierarquia` (`id_hierarquia`), KEY `id_permissao` (`id_permissao`), CONSTRAINT `hierarquia_relaciona_permissao_ibfk_1` FOREIGN KEY (`id_hierarquia`) REFERENCES `hierarquia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `hierarquia_relaciona_permissao_ibfk_2` FOREIGN KEY (`id_permissao`) REFERENCES `permissao` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ) ENGINE=InnoDB AUTO_INCREMENT=645 DEFAULT CHARSET=utf8;
CREATE TABLE `modulo` ( `id` int(11) NOT NULL AUTO_INCREMENT, `modulo` varchar(64) NOT NULL, `nome` varchar(64) NOT NULL, `id_submenu` int(11) DEFAULT NULL, `hierarquia` int(11) NOT NULL, `icone` varchar(64) NOT NULL DEFAULT 'fa-angle-right', `oculto` tinyint(1) NOT NULL DEFAULT '0', `ordem` int(11) NOT NULL DEFAULT '1000', `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`), KEY `id_submenu` (`id_submenu`) ) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
CREATE TABLE `ordem_usuario_menu` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_usuario` int(11) NOT NULL, `id_modulo` int(11) NOT NULL, `ordem` int(11) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`), KEY `id_usuario` (`id_usuario`), KEY `id_modulo` (`id_modulo`), CONSTRAINT `ordem_usuario_menu_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `ordem_usuario_menu_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `pagina_institucional` ( `id` int(11) NOT NULL AUTO_INCREMENT, `titulo` varchar(512) DEFAULT NULL, `conteudo` text NOT NULL, `exibir_cabecalho` tinyint(1) NOT NULL DEFAULT '0', `exibir_rodape` tinyint(1) NOT NULL DEFAULT '0', `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `permissao` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_modulo` int(11) NOT NULL, `permissao` varchar(64) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`), KEY `id_modulo` (`id_modulo`) ) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;
CREATE TABLE `pessoa` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_usuario` int(11) NOT NULL, `nome` varchar(64) NOT NULL, `sobrenome` varchar(256) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`), KEY `id_usuario` (`id_usuario`), CONSTRAINT `pessoa_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE `plataforma` ( `id` int(11) NOT NULL AUTO_INCREMENT, `identificador` varchar(512) NOT NULL, `tipo`	varchar(16)	NOT NULL, `nome` varchar(512) NOT NULL, `descricao` varchar(1024) NOT NULL, `ultima_atualizacao` datetime DEFAULT NULL, `ultima_publicacao` datetime DEFAULT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
CREATE TABLE `plataforma_pagina` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_plataforma` int(11) NOT NULL, `id_usuario` int(11) NOT NULL, `html` text, `ultima_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `publicado` tinyint(1) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`), KEY `id_plataforma` (`id_plataforma`), KEY `id_usuario` (`id_usuario`), CONSTRAINT `plataforma_pagina_ibfk_1` FOREIGN KEY (`id_plataforma`) REFERENCES `plataforma` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION, CONSTRAINT `plataforma_pagina_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `submenu` ( `id` int(11) NOT NULL AUTO_INCREMENT, `nome` varchar(64) NOT NULL, `icone` varchar(64) NOT NULL DEFAULT 'fa-angle-right', `ativo` tinyint(1) NOT NULL DEFAULT '1', `nome_exibicao` varchar(64) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE `url` ( `id` int(11) NOT NULL AUTO_INCREMENT, `url` varchar(512) NOT NULL, `id_controller` int(11) DEFAULT NULL, `controller` varchar(256) NOT NULL, `metodo` varchar(256) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `usuario` ( `id` int(11) NOT NULL AUTO_INCREMENT, `email` varchar(256) NOT NULL, `senha` text NOT NULL, `hierarquia` int(11) DEFAULT NULL, `acesso_admin` tinyint(1) NOT NULL DEFAULT '0', `super_admin` tinyint(1) NOT NULL DEFAULT '0', `oculto` tinyint(4) NOT NULL DEFAULT '0', `bloqueado` tinyint(4) NOT NULL DEFAULT '0', `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`), KEY `hierarquia` (`hierarquia`), CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`hierarquia`) REFERENCES `hierarquia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
INSERT INTO `configuracao` VALUES (1,'','','','','','',1,1);
INSERT INTO `hierarquia` VALUES (1,'Administrador Supremo',0,1),(2,'Administrador Plus Plus',1,1);
INSERT INTO `hierarquia_relaciona_permissao` VALUES (542,2,5,1),(543,2,6,1),(544,2,7,1),(545,2,65,1),(546,2,10,1),(547,2,11,1),(548,2,21,1),(549,2,22,1),(550,2,23,1),(551,2,24,1),(552,2,25,1),(553,2,26,1),(554,2,27,1),(555,2,28,1),(556,2,29,1),(557,2,30,1),(558,2,31,1),(559,2,32,1),(560,2,37,1),(561,2,38,1),(562,2,39,1),(563,2,40,1),(564,2,58,1),(565,2,59,1),(566,2,45,1),(567,2,46,1),(568,2,47,1),(569,2,48,1),(570,2,49,1),(571,2,50,1),(572,2,51,1),(573,2,52,1),(574,2,53,1),(575,2,54,1),(576,2,55,1),(577,2,56,1),(578,2,60,1),(579,2,61,1),(580,2,62,1),(581,2,63,1),(582,1,5,1),(583,1,6,1),(584,1,7,1),(585,1,65,1),(586,1,10,1),(587,1,11,1),(588,1,21,1),(589,1,22,1),(590,1,23,1),(591,1,24,1),(592,1,25,1),(593,1,26,1),(594,1,27,1),(595,1,28,1),(596,1,29,1),(597,1,30,1),(598,1,31,1),(599,1,32,1),(600,1,37,1),(601,1,38,1),(602,1,39,1),(603,1,40,1),(604,1,58,1),(605,1,59,1),(606,1,45,1),(607,1,46,1),(608,1,47,1),(609,1,48,1),(610,1,49,1),(611,1,50,1),(612,1,51,1),(613,1,52,1),(614,1,53,1),(615,1,54,1),(616,1,55,1),(617,1,56,1),(618,1,60,1),(619,1,61,1),(620,1,62,1),(621,1,63,1),(622,1,67,1),(623,1,68,1);
INSERT INTO `modulo` VALUES (1,'modulo','Modulos',1,0,'fa-check-square-o',0,13000,1),(2,'usuario','Usuarios',NULL,1,'fa-users',0,1000,1),(3,'configuracao','Configurações',NULL,1,'fa-cog',0,10000,1),(4,'submenu','Submenus',1,0,'fa-sitemap',0,15000,1),(5,'hierarquia','Hierarquias',NULL,1,'fa-sitemap',0,9000,1),(7,'pagina_institucional','Pagina Institucional',NULL,1,'fa-file-code-o',0,8000,1),(9,'permissao','Permissões',1,0,'fa-thumbs-o-up',0,14000,1),(17,'plataforma','HTML Cloud Editor',0,1,'fa-code',0,11000,1),(19,'url','URLs',NULL,1,'fa-link',0,10500,1);
INSERT INTO `permissao` VALUES (1,1,'criar',1),(2,1,'visualizar',1),(3,1,'editar',1),(4,1,'deletar',1),(5,2,'criar',1),(6,2,'visualizar',1),(7,2,'editar',1),(8,2,'deletar',1),(10,3,'visualizar',1),(11,3,'editar',1),(13,4,'criar',1),(14,4,'visualizar',1),(15,4,'editar',1),(16,4,'deletar',1),(17,5,'criar',1),(18,5,'visualizar',1),(19,5,'editar',1),(20,5,'deletar',1),(21,6,'criar',1),(22,6,'visualizar',1),(23,6,'editar',1),(24,6,'deletar',1),(25,7,'criar',1),(26,7,'visualizar',1),(27,7,'editar',1),(28,7,'deletar',1),(29,8,'criar',1),(30,8,'visualizar',1),(31,8,'editar',1),(32,8,'deletar',1),(33,9,'criar',1),(34,9,'visualizar',1),(35,9,'editar',1),(36,9,'deletar',1),(37,10,'criar',1),(38,10,'visualizar',1),(39,10,'editar',1),(40,10,'deletar',1),(41,11,'criar',1),(42,11,'visualizar',1),(43,11,'editar',1),(44,11,'deletar',1),(45,12,'criar',1),(46,12,'visualizar',1),(47,12,'editar',1),(48,12,'deletar',1),(49,13,'criar',1),(50,13,'visualizar',1),(51,13,'editar',1),(52,13,'deletar',1),(53,14,'criar',1),(54,14,'visualizar',1),(55,14,'editar',1),(56,14,'deletar',1),(58,10,'aprovar',1),(59,10,'reprovar',1),(60,15,'criar',1),(61,15,'visualizar',1),(62,15,'editar',1),(63,15,'deletar',1),(65,2,'remover_conceder_acesso',1),(67,17,'visualizar',1),(68,17,'editar',1),(70,18,'criar',1),(71,18,'visualizar',1),(72,18,'editar',1),(73,18,'deletar',1),(74,19,'criar',1),(75,19,'visualizar',1),(76,19,'editar',1),(77,19,'deletar',1),(78,20,'criar',1),(79,20,'visualizar',1),(80,20,'editar',1),(81,20,'deletar',1),(82,21,'criar',1),(83,21,'visualizar',1),(84,21,'editar',1),(85,21,'deletar',1);
INSERT INTO `plataforma` VALUES (1,'header','Header','Cabeçalho Padrão de Todas as Páginas','2019-09-03 22:40:20','2019-09-03 22:42:07',1),(2,'footer','Footer','Rodapé Padrão de Todas as Páginas','2019-09-03 22:40:58','2019-09-03 22:42:07',1),(3,'index','Index','Template da Página Inicial','2019-09-03 22:41:44','2019-09-03 22:42:07',1),(4,'pagina_institucional','Pagina Institucional','Template da Página Institucional','2019-02-25 23:57:56','2019-08-20 20:54:45',1);
INSERT INTO `submenu` VALUES (1,'desenvolvedor','fa-github',1,'Desenvolvedor');

CREATE TABLE  `poscaster`(
    `id`                  int(11)       NOT NULL AUTO_INCREMENT,
    `id_pessoa`           int(11)       NOT NULL,
    `ativo`               tinyint(1)    NOT NULL DEFAULT '1',
    PRIMARY               KEY (`id`),
    FOREIGN               KEY (`id_pessoa`)   REFERENCES `pessoa`   (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE table podcast(
	`id`          INT(11)        NOT NULL AUTO_INCREMENT,
	`nome`        VARCHAR(2048)  NOT NULL,
	`localizador` VARCHAR(2048)  NOT NULL,
	`descricao`   TEXT        	 NOT NULL,
	`link`        VARCHAR(1024)  NOT NULL,
	`ativo`       TINYINT(1)     NOT NULL DEFAULT '1',
	PRIMARY       KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;





CREATE TABLE `gerenciador_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo` varchar(1024) NOT NULL,
  `metodo` varchar(1024) NOT NULL,
  `parametros` varchar(1024) DEFAULT NULL,
  `horario` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `frequencia` varchar(64) NOT NULL,
  `email` varchar(1024) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8





CREATE TABLE `idioma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idioma` varchar(64) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

ALTER TABLE podcast
ADD COLUMN id_idioma INT(11) NULL AFTER descricao;









