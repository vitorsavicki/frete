-- MySQL Workbench Synchronization
-- Generated: 2016-11-26 15:00
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Vitor

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER SCHEMA `frete_imediato`  DEFAULT CHARACTER SET utf8  DEFAULT COLLATE utf8_general_ci ;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_alertaLance` (
  `idAleLan` INT(11) NOT NULL AUTO_INCREMENT,
  `statusAleLan` CHAR(1) NOT NULL,
  `dataRecebidaAleLan` DATETIME NOT NULL,
  `tb_Lance_idLan` INT(11) NOT NULL,
  PRIMARY KEY (`idAleLan`, `tb_Lance_idLan`),
  INDEX `fk_tb_alertaLance_tb_lance_idx` (`tb_Lance_idLan` ASC),
  CONSTRAINT `fk_tb_alertaLance_tb_lance`
    FOREIGN KEY (`tb_Lance_idLan`)
    REFERENCES `frete_imediato`.`tb_lance` (`idLan`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_lance` (
  `idLan` INT(11) NOT NULL AUTO_INCREMENT,
  `valorLan` DECIMAL(9,3) NULL DEFAULT NULL,
  `dataLan` DATETIME NULL DEFAULT NULL,
  `tb_transporte_idTansp` INT(11) NOT NULL,
  `tb_pessoa_idPes` INT(11) NOT NULL,
  `vencedorLan` CHAR(1) NULL DEFAULT NULL,
  PRIMARY KEY (`idLan`, `tb_transporte_idTansp`, `tb_pessoa_idPes`),
  INDEX `fk_tb_lance_tb_transporte1_idx` (`tb_transporte_idTansp` ASC),
  INDEX `fk_tb_lance_tb_pessoa1_idx` (`tb_pessoa_idPes` ASC),
  CONSTRAINT `fk_tb_lance_tb_transporte1`
    FOREIGN KEY (`tb_transporte_idTansp`)
    REFERENCES `frete_imediato`.`tb_transporte` (`idTansp`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_lance_tb_pessoa1`
    FOREIGN KEY (`tb_pessoa_idPes`)
    REFERENCES `frete_imediato`.`tb_pessoa` (`idPes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = eucjpms;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_transporte` (
  `idTansp` INT(11) NOT NULL AUTO_INCREMENT,
  `descricaoTransp` VARCHAR(255) NOT NULL,
  `dataCadastroTransp` DATETIME NOT NULL,
  `tb_statusTransp_idStaTransp` INT(11) NOT NULL,
  `comentarioAdiTransp` VARCHAR(500) NULL DEFAULT NULL,
  `numAjudantes` INT(11) NULL DEFAULT NULL,
  `tb_categoria_idCat` INT(11) NOT NULL,
  `tb_pessoa_idPes` INT(11) NOT NULL,
  `tb_endereco_transporte_idEndTran` INT(11) NOT NULL,
  `precoMaxiTransp` DECIMAL(9,2) NULL DEFAULT NULL,
  `dataRetiradaTransp` DATETIME NOT NULL,
  `horaRetiradaTransp` TIME(1) NULL DEFAULT NULL,
  `notivoCancelamentoTransp` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`idTansp`, `tb_statusTransp_idStaTransp`, `tb_categoria_idCat`, `tb_pessoa_idPes`, `tb_endereco_transporte_idEndTran`),
  INDEX `fk_tb_transporte_tb_statusTransp1_idx` (`tb_statusTransp_idStaTransp` ASC),
  INDEX `fk_tb_transporte_tb_categoria1_idx` (`tb_categoria_idCat` ASC),
  INDEX `fk_tb_transporte_tb_pessoa1_idx` (`tb_pessoa_idPes` ASC),
  INDEX `fk_tb_transporte_tb_endereco_transporte1_idx` (`tb_endereco_transporte_idEndTran` ASC),
  CONSTRAINT `fk_tb_transporte_tb_statusTransp1`
    FOREIGN KEY (`tb_statusTransp_idStaTransp`)
    REFERENCES `frete_imediato`.`tb_statusTransp` (`idStaTransp`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_transporte_tb_categoria1`
    FOREIGN KEY (`tb_categoria_idCat`)
    REFERENCES `frete_imediato`.`tb_categoria` (`idCat`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_transporte_tb_pessoa1`
    FOREIGN KEY (`tb_pessoa_idPes`)
    REFERENCES `frete_imediato`.`tb_pessoa` (`idPes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_transporte_tb_endereco_transporte1`
    FOREIGN KEY (`tb_endereco_transporte_idEndTran`)
    REFERENCES `frete_imediato`.`tb_endereco_transporte` (`idEndTran`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_pessoa` (
  `idPes` INT(11) NOT NULL AUTO_INCREMENT,
  `primeiroNomePes` VARCHAR(255) NOT NULL,
  `sobreNomePes` VARCHAR(255) NOT NULL,
  `emailPes` VARCHAR(255) NOT NULL,
  `senhaPes` VARCHAR(255) NOT NULL,
  `cpfCnpjPes` VARCHAR(20) NOT NULL,
  `fotoPes` VARCHAR(255) NULL DEFAULT NULL,
  `dataCadastroPes` DATETIME NOT NULL,
  `telefoneFixoPes` VARCHAR(20) NULL DEFAULT NULL,
  `telefoneCelularPes` VARCHAR(20) NULL DEFAULT NULL,
  `tb_endereco_idEnd` INT(11) NOT NULL,
  `tb_perfil_idPer` INT(11) NOT NULL,
  `tb_Status_idSta` INT(11) NOT NULL,
  PRIMARY KEY (`idPes`, `tb_endereco_idEnd`, `tb_perfil_idPer`, `tb_Status_idSta`),
  INDEX `fk_tb_pessoa_tb_endereco1_idx` (`tb_endereco_idEnd` ASC),
  INDEX `fk_tb_pessoa_tb_perfil1_idx` (`tb_perfil_idPer` ASC),
  INDEX `fk_tb_pessoa_tb_status1_idx` (`tb_Status_idSta` ASC),
  CONSTRAINT `fk_tb_pessoa_tb_endereco1`
    FOREIGN KEY (`tb_endereco_idEnd`)
    REFERENCES `frete_imediato`.`tb_endereco` (`idEnd`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_pessoa_tb_perfil1`
    FOREIGN KEY (`tb_perfil_idPer`)
    REFERENCES `frete_imediato`.`tb_perfil` (`idPer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_pessoa_tb_status1`
    FOREIGN KEY (`tb_Status_idSta`)
    REFERENCES `frete_imediato`.`tb_status` (`idSta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_alertaMensagem` (
  `idAleMen` INT(11) NOT NULL AUTO_INCREMENT,
  `statusAleMen` CHAR(1) NOT NULL,
  `dataRecebidadeAleMen` DATETIME NOT NULL,
  `tb_mensagem_idMen` INT(11) NOT NULL,
  PRIMARY KEY (`idAleMen`, `tb_mensagem_idMen`),
  INDEX `fk_tb_alertaMensagem_tb_mensagem1_idx` (`tb_mensagem_idMen` ASC),
  CONSTRAINT `fk_tb_alertaMensagem_tb_mensagem1`
    FOREIGN KEY (`tb_mensagem_idMen`)
    REFERENCES `frete_imediato`.`tb_mensagem` (`idMen`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_mensagem` (
  `idMen` INT(11) NOT NULL AUTO_INCREMENT,
  `conteudoMen` VARCHAR(255) NOT NULL,
  `dataMen` DATETIME NOT NULL,
  `tb_pessoa_idPes` INT(11) NOT NULL,
  `tb_lance_idLan` INT(11) NOT NULL,
  PRIMARY KEY (`idMen`, `tb_pessoa_idPes`, `tb_lance_idLan`),
  INDEX `fk_tb_mensagem_tb_pessoa1_idx` (`tb_pessoa_idPes` ASC),
  INDEX `fk_tb_mensagem_tb_lance1_idx` (`tb_lance_idLan` ASC),
  CONSTRAINT `fk_tb_mensagem_tb_pessoa1`
    FOREIGN KEY (`tb_pessoa_idPes`)
    REFERENCES `frete_imediato`.`tb_pessoa` (`idPes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_mensagem_tb_lance1`
    FOREIGN KEY (`tb_lance_idLan`)
    REFERENCES `frete_imediato`.`tb_lance` (`idLan`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_avaliacao` (
  `idAva` INT(11) NOT NULL AUTO_INCREMENT,
  `dataAva` DATETIME NOT NULL,
  `conteudoAva` VARCHAR(500) NULL DEFAULT NULL,
  `tb_status_idSta` INT(11) NOT NULL,
  `tb_transporte_idTansp` INT(11) NOT NULL,
  `valorAva1` DECIMAL(9,3) NULL DEFAULT NULL,
  `valorAva2` DECIMAL(9,3) NULL DEFAULT NULL,
  `valorAva3` DECIMAL(9,3) NULL DEFAULT NULL,
  `valorAva4` DECIMAL(9,3) NULL DEFAULT NULL,
  `valorAva5` DECIMAL(9,3) NULL DEFAULT NULL,
  `valorAva6` DECIMAL(9,3) NULL DEFAULT NULL,
  `valorAva7` DECIMAL(9,3) NULL DEFAULT NULL,
  `valorAva8` DECIMAL(9,3) NULL DEFAULT NULL,
  `valorAva9` DECIMAL(9,3) NULL DEFAULT NULL,
  `valorAva10` DECIMAL(9,3) NULL DEFAULT NULL,
  PRIMARY KEY (`idAva`, `tb_status_idSta`, `tb_transporte_idTansp`),
  INDEX `fk_tb_avaliacao_tb_status1_idx` (`tb_status_idSta` ASC),
  INDEX `fk_tb_avaliacao_tb_transporte1_idx` (`tb_transporte_idTansp` ASC),
  CONSTRAINT `fk_tb_avaliacao_tb_status1`
    FOREIGN KEY (`tb_status_idSta`)
    REFERENCES `frete_imediato`.`tb_status` (`idSta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_avaliacao_tb_transporte1`
    FOREIGN KEY (`tb_transporte_idTansp`)
    REFERENCES `frete_imediato`.`tb_transporte` (`idTansp`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_status` (
  `idSta` INT(11) NOT NULL AUTO_INCREMENT,
  `nomeSta` VARCHAR(255) NOT NULL,
  `codigoSta` CHAR(1) NOT NULL,
  PRIMARY KEY (`idSta`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_boleto` (
  `idBol` INT(11) NOT NULL AUTO_INCREMENT,
  `diasPrazoBol` INT(11) NOT NULL,
  `taxabol` DECIMAL(18,2) NOT NULL,
  `dataVencBol` DATETIME NOT NULL,
  `valorBol` DECIMAL(18,2) NOT NULL,
  `valorJurosBol` DECIMAL(18,2) NOT NULL,
  `numeroBol` INT(11) NOT NULL,
  `dataEmissaoBol` DATETIME NOT NULL,
  `dataInclusaoBol` DATETIME NOT NULL,
  `valorTotalBol` DECIMAL(18,2) NOT NULL,
  `nomeClienteBol` VARCHAR(255) NULL DEFAULT NULL,
  `endClienteBol` VARCHAR(255) NULL DEFAULT NULL,
  `end2ClienteBol` VARCHAR(255) NULL DEFAULT NULL,
  `demonstrativo1Bol` VARCHAR(255) NULL DEFAULT NULL,
  `demonstrativo2Bol` VARCHAR(255) NULL DEFAULT NULL,
  `demonstrativo3Bol` VARCHAR(255) NULL DEFAULT NULL,
  `instrucao1Bol` VARCHAR(255) NULL DEFAULT NULL,
  `instrucao2Bol` VARCHAR(255) NULL DEFAULT NULL,
  `instrucao3Bol` VARCHAR(255) NULL DEFAULT NULL,
  `instrucao4Bol` VARCHAR(255) NULL DEFAULT NULL,
  `quantidadeBol` INT(11) NULL DEFAULT NULL,
  `valorUnitBol` DECIMAL(18,2) NULL DEFAULT NULL,
  `aceiteBol` VARCHAR(10) NULL DEFAULT NULL,
  `especieBol` VARCHAR(10) NULL DEFAULT NULL,
  `codigoClienteBol` INT(11) NOT NULL,
  `carteiraBol` VARCHAR(10) NOT NULL,
  `indentificacaoBol` VARCHAR(255) NULL DEFAULT NULL,
  `cnpjCedenteBol` VARCHAR(255) NULL DEFAULT NULL,
  `endCedenteBol` VARCHAR(255) NULL DEFAULT NULL,
  `cidadeCedenteBol` VARCHAR(255) NULL DEFAULT NULL,
  `ufCedenteBol` VARCHAR(255) NULL DEFAULT NULL,
  `cedenteBol` VARCHAR(255) NULL DEFAULT NULL,
  `idMensa` INT(11) NOT NULL,
  PRIMARY KEY (`idBol`),
  INDEX `fk_tb_boleto_tb_mensalidade1_idx` (`idMensa` ASC),
  CONSTRAINT `fk_tb_boleto_tb_mensalidade1`
    FOREIGN KEY (`idMensa`)
    REFERENCES `frete_imediato`.`tb_mensalidade` (`idMensa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_mensalidade` (
  `idMensa` INT(11) NOT NULL AUTO_INCREMENT,
  `dataPagamentoMensa` DATETIME NULL DEFAULT NULL,
  `dataVencimentoMensa` DATETIME NULL DEFAULT NULL,
  `valorMensa` DECIMAL(18,3) NULL DEFAULT NULL,
  `tb_pessoa_idPes` INT(11) NOT NULL,
  `tb_situacaoMensalidade_idSit` INT(11) NOT NULL,
  PRIMARY KEY (`idMensa`),
  INDEX `fk_tb_mensalidade_tb_pessoa1_idx` (`tb_pessoa_idPes` ASC),
  INDEX `fk_tb_mensalidade_tb_situacaoMensalidade1_idx` (`tb_situacaoMensalidade_idSit` ASC),
  CONSTRAINT `fk_tb_mensalidade_tb_pessoa1`
    FOREIGN KEY (`tb_pessoa_idPes`)
    REFERENCES `frete_imediato`.`tb_pessoa` (`idPes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_mensalidade_tb_situacaoMensalidade1`
    FOREIGN KEY (`tb_situacaoMensalidade_idSit`)
    REFERENCES `frete_imediato`.`tb_situacaoMensalidade` (`idSit`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_situacaoMensalidade` (
  `idSit` INT(11) NOT NULL AUTO_INCREMENT,
  `descricaoSit` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`idSit`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_categoria` (
  `idCat` INT(11) NOT NULL AUTO_INCREMENT,
  `nomeCat` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`idCat`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_cidade` (
  `idCid` INT(11) NOT NULL AUTO_INCREMENT,
  `nomeCid` VARCHAR(255) NOT NULL,
  `tb_Estado_idEst` INT(11) NOT NULL,
  PRIMARY KEY (`idCid`),
  INDEX `fk_tb_cidade_tb_estado1_idx` (`tb_Estado_idEst` ASC),
  CONSTRAINT `fk_tb_cidade_tb_estado1`
    FOREIGN KEY (`tb_Estado_idEst`)
    REFERENCES `frete_imediato`.`tb_estado` (`idEst`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_estado` (
  `idEst` INT(11) NOT NULL AUTO_INCREMENT,
  `nomeEst` VARCHAR(45) NOT NULL,
  `ufEst` VARCHAR(5) NOT NULL,
  `tb_pais_idPais` INT(11) NOT NULL,
  PRIMARY KEY (`idEst`),
  INDEX `fk_tb_estado_tb_pais1_idx` (`tb_pais_idPais` ASC),
  CONSTRAINT `fk_tb_estado_tb_pais1`
    FOREIGN KEY (`tb_pais_idPais`)
    REFERENCES `frete_imediato`.`tb_pais` (`idPais`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_conteudo_transporte` (
  `idConTran` INT(11) NOT NULL AUTO_INCREMENT,
  `descricaoItemConTran` VARCHAR(255) NULL DEFAULT NULL,
  `qtdeConTran` INT(11) NULL DEFAULT NULL,
  `alturaConTran` DECIMAL(9,3) NULL DEFAULT NULL,
  `larguraConTran` DECIMAL(9,3) NULL DEFAULT NULL,
  `comprimentoConTran` DECIMAL(9,3) NULL DEFAULT NULL,
  `pesoConTran` DECIMAL(9,3) NULL DEFAULT NULL,
  `tb_item_idItem` INT(11) NOT NULL,
  `tb_transporte_idTansp` INT(11) NOT NULL,
  PRIMARY KEY (`idConTran`),
  INDEX `fk_tb_conteudo_transporte_tb_item1_idx` (`tb_item_idItem` ASC),
  INDEX `fk_tb_conteudo_transporte_tb_transporte1_idx` (`tb_transporte_idTansp` ASC),
  CONSTRAINT `fk_tb_conteudo_transporte_tb_item1`
    FOREIGN KEY (`tb_item_idItem`)
    REFERENCES `frete_imediato`.`tb_item` (`idItem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_conteudo_transporte_tb_transporte1`
    FOREIGN KEY (`tb_transporte_idTansp`)
    REFERENCES `frete_imediato`.`tb_transporte` (`idTansp`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_item` (
  `idItem` INT(11) NOT NULL AUTO_INCREMENT,
  `nomeItem` VARCHAR(45) NULL DEFAULT NULL,
  `tb_categoria_idCat` INT(11) NOT NULL,
  PRIMARY KEY (`idItem`),
  INDEX `fk_tb_item_tb_categoria1_idx` (`tb_categoria_idCat` ASC),
  CONSTRAINT `fk_tb_item_tb_categoria1`
    FOREIGN KEY (`tb_categoria_idCat`)
    REFERENCES `frete_imediato`.`tb_categoria` (`idCat`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_endereco` (
  `idEnd` INT(11) NOT NULL AUTO_INCREMENT,
  `cepEnd` CHAR(10) NULL DEFAULT NULL,
  `ruaEnd` VARCHAR(255) NOT NULL,
  `bairroEnd` VARCHAR(255) NOT NULL,
  `complementoEnd` VARCHAR(255) NULL DEFAULT NULL,
  `tb_cidade_idCid` INT(11) NOT NULL,
  `tb_estado_idEst` INT(11) NOT NULL,
  PRIMARY KEY (`idEnd`),
  INDEX `fk_tb_endereco_tb_cidade1_idx` (`tb_cidade_idCid` ASC),
  INDEX `fk_tb_endereco_tb_estado1_idx` (`tb_estado_idEst` ASC),
  CONSTRAINT `fk_tb_endereco_tb_cidade1`
    FOREIGN KEY (`tb_cidade_idCid`)
    REFERENCES `frete_imediato`.`tb_cidade` (`idCid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_endereco_tb_estado1`
    FOREIGN KEY (`tb_estado_idEst`)
    REFERENCES `frete_imediato`.`tb_estado` (`idEst`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_endereco_transporte` (
  `idEndTran` INT(11) NOT NULL AUTO_INCREMENT,
  `cepOrigemEndTran` VARCHAR(10) NULL DEFAULT NULL,
  `cepDestinoEndTran` VARCHAR(10) NULL DEFAULT NULL,
  `ruaOrigemEndTran` VARCHAR(255) NOT NULL,
  `ruaDestinoEndTran` VARCHAR(255) NOT NULL,
  `bairroOrigemEndTran` VARCHAR(255) NOT NULL,
  `bairroDestinoEndTran` VARCHAR(255) NOT NULL,
  `tb_cidadeOrigem_idCid` INT(11) NOT NULL,
  `tb_cidadeDestino_idCid` INT(11) NOT NULL,
  PRIMARY KEY (`idEndTran`),
  INDEX `fk_tb_endereco_transporte_tb_cidade1_idx` (`tb_cidadeOrigem_idCid` ASC),
  INDEX `fk_tb_endereco_transporte_tb_cidade2_idx` (`tb_cidadeDestino_idCid` ASC),
  CONSTRAINT `fk_tb_endereco_transporte_tb_cidade1`
    FOREIGN KEY (`tb_cidadeOrigem_idCid`)
    REFERENCES `frete_imediato`.`tb_cidade` (`idCid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_endereco_transporte_tb_cidade2`
    FOREIGN KEY (`tb_cidadeDestino_idCid`)
    REFERENCES `frete_imediato`.`tb_cidade` (`idCid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_imagens_transporte` (
  `idImgTran` INT(11) NOT NULL AUTO_INCREMENT,
  `caminhoImgTran` VARCHAR(255) NULL DEFAULT NULL,
  `tb_transporte_idTansp` INT(11) NOT NULL,
  PRIMARY KEY (`idImgTran`),
  INDEX `fk_tb_imagens_transporte_tb_transporte1_idx` (`tb_transporte_idTansp` ASC),
  CONSTRAINT `fk_tb_imagens_transporte_tb_transporte1`
    FOREIGN KEY (`tb_transporte_idTansp`)
    REFERENCES `frete_imediato`.`tb_transporte` (`idTansp`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_log` (
  `idLog` INT(11) NOT NULL AUTO_INCREMENT,
  `dataLog` DATETIME NOT NULL,
  `tb_pessoa_idPes` INT(11) NOT NULL,
  PRIMARY KEY (`idLog`),
  INDEX `fk_tb_log_tb_pessoa1_idx` (`tb_pessoa_idPes` ASC),
  CONSTRAINT `fk_tb_log_tb_pessoa1`
    FOREIGN KEY (`tb_pessoa_idPes`)
    REFERENCES `frete_imediato`.`tb_pessoa` (`idPes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_pais` (
  `idPais` INT(11) NOT NULL AUTO_INCREMENT,
  `nomePais` VARCHAR(255) NOT NULL,
  `siglaPais` VARCHAR(5) NOT NULL,
  PRIMARY KEY (`idPais`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_perfil` (
  `idPer` INT(11) NOT NULL AUTO_INCREMENT,
  `nomePer` VARCHAR(255) NOT NULL,
  `codigoPer` CHAR(1) NOT NULL,
  PRIMARY KEY (`idPer`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_pergunta_pesquisa` (
  `idPqa` INT(11) NOT NULL AUTO_INCREMENT,
  `nomePqa` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`idPqa`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_pessoa_voucher` (
  `idPesVou` INT(11) NOT NULL AUTO_INCREMENT,
  `tb_voucher_idVou` INT(11) NOT NULL,
  `tb_pessoa_idPes` INT(11) NOT NULL,
  PRIMARY KEY (`idPesVou`, `tb_voucher_idVou`, `tb_pessoa_idPes`),
  INDEX `fk_tb_pessoa_voucher_tb_voucher1_idx` (`tb_voucher_idVou` ASC),
  INDEX `fk_tb_pessoa_voucher_tb_pessoa1_idx` (`tb_pessoa_idPes` ASC),
  CONSTRAINT `fk_tb_pessoa_voucher_tb_voucher1`
    FOREIGN KEY (`tb_voucher_idVou`)
    REFERENCES `frete_imediato`.`tb_voucher` (`idVou`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_pessoa_voucher_tb_pessoa1`
    FOREIGN KEY (`tb_pessoa_idPes`)
    REFERENCES `frete_imediato`.`tb_pessoa` (`idPes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_voucher` (
  `idVou` INT(11) NOT NULL AUTO_INCREMENT,
  `codigoVou` VARCHAR(10) NOT NULL,
  `dataValidadeVou` DATETIME NOT NULL,
  PRIMARY KEY (`idVou`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_resposta_pesquisa` (
  `idResPes` INT(11) NOT NULL AUTO_INCREMENT,
  `tb_pergunta_pesquisa_idPqa` INT(11) NOT NULL,
  `tb_pessoa_idPes` INT(11) NOT NULL,
  PRIMARY KEY (`idResPes`, `tb_pergunta_pesquisa_idPqa`, `tb_pessoa_idPes`),
  INDEX `fk_tb_resposta_pesquisa_tb_pergunta_pesquisa1_idx` (`tb_pergunta_pesquisa_idPqa` ASC),
  INDEX `fk_tb_resposta_pesquisa_tb_pessoa1_idx` (`tb_pessoa_idPes` ASC),
  CONSTRAINT `fk_tb_resposta_pesquisa_tb_pergunta_pesquisa1`
    FOREIGN KEY (`tb_pergunta_pesquisa_idPqa`)
    REFERENCES `frete_imediato`.`tb_pergunta_pesquisa` (`idPqa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_resposta_pesquisa_tb_pessoa1`
    FOREIGN KEY (`tb_pessoa_idPes`)
    REFERENCES `frete_imediato`.`tb_pessoa` (`idPes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `frete_imediato`.`tb_statusTransp` (
  `idStaTransp` INT(11) NOT NULL AUTO_INCREMENT,
  `nomeStaTransp` VARCHAR(255) NOT NULL,
  `codigoStaTransp` CHAR(1) NULL DEFAULT NULL,
  PRIMARY KEY (`idStaTransp`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
