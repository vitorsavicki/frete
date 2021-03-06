USE [freteimediato]
GO
/****** Object:  Table [dbo].[tb_alertaLance]    Script Date: 08/02/2016 17:56:34 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_alertaLance](
	[idAleLan] [int] IDENTITY(1,1) NOT NULL,
	[statusAleLan] [char](1) NOT NULL,
	[dataRecebidaAleLan] [datetime] NOT NULL,
	[tb_Lance_idLan] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idAleLan] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_alertaMensagem]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_alertaMensagem](
	[idAleMen] [int] IDENTITY(1,1) NOT NULL,
	[statusAleMen] [char](1) NOT NULL,
	[dataRecebidaAleMen] [datetime] NOT NULL,
	[tb_mensagem_idMen] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idAleMen] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_avaliacao]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_avaliacao](
	[idAva] [int] IDENTITY(1,1) NOT NULL,
	[dataAva] [datetime] NOT NULL,
	[conteudoAva] [varchar](500) NOT NULL,
	[tb_status_idStaAva] [int] NOT NULL,
	[tb_transporte_idTransp] [int] NOT NULL,
	[valorAva1] [decimal](9, 3) NULL,
	[valorAva2] [decimal](9, 3) NULL,
	[valorAva4] [decimal](9, 3) NULL,
	[valorAva5] [decimal](9, 3) NULL,
	[valorAva3] [decimal](9, 3) NULL,
	[valorAva6] [decimal](9, 3) NULL,
	[valorAva7] [decimal](9, 3) NULL,
	[valorAva8] [decimal](9, 3) NULL,
	[valorAva9] [decimal](9, 3) NULL,
	[valorAva10] [decimal](9, 3) NULL,
PRIMARY KEY CLUSTERED 
(
	[idAva] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_boleto]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING OFF
GO
CREATE TABLE [dbo].[tb_boleto](
	[idBol] [int] IDENTITY(1,1) NOT NULL,
	[diasPrazoBol] [int] NOT NULL,
	[taxaBol] [numeric](18, 2) NOT NULL,
	[dataVencBol] [datetime] NOT NULL,
	[valorBol] [numeric](18, 2) NOT NULL,
	[valorJurosBol] [numeric](18, 2) NOT NULL,
	[numeroBol] [int] NOT NULL,
	[dataEmissaoBol] [datetime] NOT NULL,
	[dataInclusaoBol] [datetime] NOT NULL,
	[valorTotalBol] [numeric](18, 2) NOT NULL,
	[nomeClienteBol] [varchar](255) NULL,
	[endClienteBol] [varchar](255) NULL,
	[end2ClienteBol] [varchar](255) NULL,
	[demonstrativo1Bol] [varchar](255) NULL,
	[demonstrativo2Bol] [varchar](255) NULL,
	[demonstrativo3Bol] [varchar](255) NULL,
	[instrucao1Bol] [varchar](255) NULL,
	[instrucao2Bol] [varchar](255) NULL,
	[instrucao3Bol] [varchar](255) NULL,
	[instrucao4Bol] [varchar](255) NULL,
	[quantidadeBol] [int] NULL,
	[valorUnitBol] [numeric](18, 2) NULL,
	[aceiteBol] [varchar](10) NULL,
	[especieBol] [varchar](10) NULL,
	[codigoClienteBol] [int] NOT NULL,
	[carteiraBol] [varchar](10) NOT NULL,
	[identificacaoBol] [varchar](255) NULL,
	[cnpjCedenteBol] [varchar](255) NULL,
	[endCedenteBol] [varchar](255) NULL,
	[cidadeCedenteBol] [varchar](255) NULL,
	[ufCedenteBol] [varchar](2) NULL,
	[cedenteBol] [varchar](255) NULL,
	[idMensa] [int] NULL,
 CONSTRAINT [PK_tb_boleto] PRIMARY KEY CLUSTERED 
(
	[idBol] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_categoria]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_categoria](
	[idCat] [int] IDENTITY(1,1) NOT NULL,
	[nomeCat] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idCat] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_cidade]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tb_cidade](
	[idCid] [int] NOT NULL,
	[nomeCid] [nvarchar](120) NULL,
	[tb_Estado_idEst] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idCid] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[tb_conteudo_transporte]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_conteudo_transporte](
	[idConTran] [int] IDENTITY(1,1) NOT NULL,
	[descricaoItemConTran] [varchar](255) NULL,
	[qtdeConTran] [decimal](9, 3) NULL,
	[alturaConTran] [decimal](9, 3) NULL,
	[larguraConTran] [decimal](9, 3) NULL,
	[comprimentoConTran] [decimal](9, 3) NULL,
	[pesoConTran] [decimal](9, 3) NULL,
	[tb_item_idItem] [int] NOT NULL,
	[tb_transporte_idTransp] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idConTran] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_endereco]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_endereco](
	[idEnd] [int] IDENTITY(1,1) NOT NULL,
	[cepEnd] [varchar](255) NULL,
	[ruaEnd] [varchar](255) NOT NULL,
	[bairroEnd] [varchar](255) NOT NULL,
	[complementoEnd] [varchar](255) NULL,
	[tb_Cidade_idCid] [int] NULL,
	[tb_Estado_idEst] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[idEnd] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_endereco_transporte]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_endereco_transporte](
	[idEndTran] [int] IDENTITY(1,1) NOT NULL,
	[cepOrigemEndTran] [varchar](10) NOT NULL,
	[cepDestinoEndTran] [varchar](10) NOT NULL,
	[ruaOrigemEndTran] [varchar](255) NOT NULL,
	[ruaDestinoEndTran] [varchar](255) NOT NULL,
	[bairroOrigemEndTran] [varchar](255) NOT NULL,
	[bairroDestinoEndTran] [varchar](255) NOT NULL,
	[tb_cidadeOrigem_IdCid] [int] NOT NULL,
	[tb_cidadeDestino_IdCid] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idEndTran] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_estado]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_estado](
	[idEst] [int] IDENTITY(1,1) NOT NULL,
	[nomeEst] [nvarchar](75) NULL,
	[ufEst] [varchar](5) NOT NULL,
	[tb_Pais_idPais] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idEst] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_imagens_transporte]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_imagens_transporte](
	[idImgTran] [int] IDENTITY(1,1) NOT NULL,
	[caminhoImg] [varchar](255) NULL,
	[tb_transporte_idTransp] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idImgTran] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_item]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_item](
	[idItem] [int] IDENTITY(1,1) NOT NULL,
	[nomeItem] [varchar](255) NOT NULL,
	[tb_categoria_idCat] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idItem] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_lance]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_lance](
	[idLan] [int] IDENTITY(1,1) NOT NULL,
	[valorLan] [decimal](9, 3) NULL,
	[dataLan] [datetime] NULL,
	[tb_transporte_idTransp] [int] NOT NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
	[vencedorLan] [char](1) NULL,
PRIMARY KEY CLUSTERED 
(
	[idLan] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_log]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tb_log](
	[idLog] [int] IDENTITY(1,1) NOT NULL,
	[dataLog] [datetime] NOT NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idLog] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[tb_mensagem]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_mensagem](
	[idMen] [int] IDENTITY(1,1) NOT NULL,
	[conteudoMen] [varchar](255) NOT NULL,
	[dataMen] [datetime] NOT NULL,
	[tb_pessoa_idPes] [int] NULL,
	[tb_lance_idLan] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idMen] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_mensalidade]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tb_mensalidade](
	[idMensa] [int] IDENTITY(1,1) NOT NULL,
	[dataVencimentoMensa] [datetime] NULL,
	[dataPagamentoMensa] [datetime] NULL,
	[valorMensa] [decimal](9, 3) NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
	[tb_situacaoMensalidade_idSit] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[idMensa] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[tb_pais]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_pais](
	[idPais] [int] IDENTITY(1,1) NOT NULL,
	[nomePais] [varchar](60) NOT NULL,
	[siglaPais] [varchar](10) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idPais] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_perfil]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_perfil](
	[idPer] [int] IDENTITY(1,1) NOT NULL,
	[nomePer] [varchar](45) NOT NULL,
	[codigoPer] [char](1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idPer] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_pergunta_pesquisa]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_pergunta_pesquisa](
	[idPqa] [int] IDENTITY(1,1) NOT NULL,
	[nomePqa] [varchar](200) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idPqa] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_pessoa]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_pessoa](
	[idPes] [int] IDENTITY(1,1) NOT NULL,
	[primeiroNomePes] [varchar](255) NOT NULL,
	[sobreNomePes] [varchar](255) NOT NULL,
	[emailPes] [varchar](255) NOT NULL,
	[senhaPes] [varchar](255) NOT NULL,
	[cpfCnpjPes] [varchar](255) NOT NULL,
	[fotoPes] [varchar](255) NULL,
	[dataCadastroPes] [datetime] NOT NULL,
	[telefoneFixoPes] [varchar](20) NULL,
	[telefoneCelularPes] [varchar](20) NULL,
	[tb_endereco_idEnd] [int] NULL,
	[tb_perfil_idPer] [int] NOT NULL,
	[tb_Status_idSta] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[idPes] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_pessoa_voucher]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tb_pessoa_voucher](
	[idPesVou] [int] IDENTITY(1,1) NOT NULL,
	[tb_voucher_idVou] [int] NOT NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idPesVou] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[tb_resposta_pesquisa]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tb_resposta_pesquisa](
	[idResPes] [int] IDENTITY(1,1) NOT NULL,
	[tb_pergunta_pesquisa_idPqa] [int] NOT NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idResPes] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
/****** Object:  Table [dbo].[tb_situacaoMensalidade]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_situacaoMensalidade](
	[idSit] [int] IDENTITY(1,1) NOT NULL,
	[descricaoSit] [varchar](255) NULL,
 CONSTRAINT [PK_tb_tb_situacaoMensalidade] PRIMARY KEY CLUSTERED 
(
	[idSit] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_status]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_status](
	[idSta] [int] IDENTITY(1,1) NOT NULL,
	[nomeSta] [varchar](255) NULL,
	[codigoSta] [char](1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idSta] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_statusTransp]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_statusTransp](
	[idStaTransp] [int] IDENTITY(1,1) NOT NULL,
	[nomeStaTransp] [varchar](255) NULL,
	[codigoStaTransp] [char](1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idStaTransp] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_transporte]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_transporte](
	[idTransp] [int] IDENTITY(1,1) NOT NULL,
	[descricaoTransp] [varchar](255) NOT NULL,
	[dataCadastroTransp] [datetime] NOT NULL,
	[tb_statusTransp_idStaTransp] [int] NULL,
	[comentarioAdiTransp] [varchar](500) NULL,
	[numAjudantesTransp] [int] NULL,
	[tb_categoria_idCat] [int] NOT NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
	[tb_endereco_transporte_idEndTran] [int] NOT NULL,
	[precoMaxiTransp] [decimal](9, 2) NULL,
	[dataRetiradaTransp] [datetime] NULL,
	[horaRetiradaTransp] [time](7) NULL,
	[motivoCancelamentoTransp] [varchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[idTransp] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[tb_voucher]    Script Date: 08/02/2016 17:56:35 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[tb_voucher](
	[idVou] [int] IDENTITY(1,1) NOT NULL,
	[codigoVou] [varchar](10) NOT NULL,
	[dataValidadeVou] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idVou] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
ALTER TABLE [dbo].[tb_boleto] ADD  DEFAULT ('R$') FOR [especieBol]
GO
ALTER TABLE [dbo].[tb_boleto] ADD  DEFAULT ('CNR') FOR [carteiraBol]
GO
ALTER TABLE [dbo].[tb_alertaLance]  WITH CHECK ADD  CONSTRAINT [fk_tb_lance_tb_alertaLance1] FOREIGN KEY([tb_Lance_idLan])
REFERENCES [dbo].[tb_lance] ([idLan])
GO
ALTER TABLE [dbo].[tb_alertaLance] CHECK CONSTRAINT [fk_tb_lance_tb_alertaLance1]
GO
ALTER TABLE [dbo].[tb_alertaMensagem]  WITH CHECK ADD  CONSTRAINT [fk_tb_mensagem_tb_alertaMensagem1] FOREIGN KEY([tb_mensagem_idMen])
REFERENCES [dbo].[tb_mensagem] ([idMen])
GO
ALTER TABLE [dbo].[tb_alertaMensagem] CHECK CONSTRAINT [fk_tb_mensagem_tb_alertaMensagem1]
GO
ALTER TABLE [dbo].[tb_avaliacao]  WITH CHECK ADD  CONSTRAINT [fk_tb_avaliacao_tb_idTransp] FOREIGN KEY([tb_transporte_idTransp])
REFERENCES [dbo].[tb_transporte] ([idTransp])
GO
ALTER TABLE [dbo].[tb_avaliacao] CHECK CONSTRAINT [fk_tb_avaliacao_tb_idTransp]
GO
ALTER TABLE [dbo].[tb_avaliacao]  WITH CHECK ADD  CONSTRAINT [fk_tb_avaliacao_tb_status_avaliacao1] FOREIGN KEY([tb_status_idStaAva])
REFERENCES [dbo].[tb_status] ([idSta])
GO
ALTER TABLE [dbo].[tb_avaliacao] CHECK CONSTRAINT [fk_tb_avaliacao_tb_status_avaliacao1]
GO
ALTER TABLE [dbo].[tb_boleto]  WITH CHECK ADD  CONSTRAINT [FK_tb_boleto_idMensa] FOREIGN KEY([idMensa])
REFERENCES [dbo].[tb_mensalidade] ([idMensa])
GO
ALTER TABLE [dbo].[tb_boleto] CHECK CONSTRAINT [FK_tb_boleto_idMensa]
GO
ALTER TABLE [dbo].[tb_cidade]  WITH CHECK ADD  CONSTRAINT [fk_tb_cidade_tb_estado] FOREIGN KEY([tb_Estado_idEst])
REFERENCES [dbo].[tb_estado] ([idEst])
GO
ALTER TABLE [dbo].[tb_cidade] CHECK CONSTRAINT [fk_tb_cidade_tb_estado]
GO
ALTER TABLE [dbo].[tb_conteudo_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_conteudo_transporte_tb_item1] FOREIGN KEY([tb_item_idItem])
REFERENCES [dbo].[tb_item] ([idItem])
GO
ALTER TABLE [dbo].[tb_conteudo_transporte] CHECK CONSTRAINT [fk_tb_conteudo_transporte_tb_item1]
GO
ALTER TABLE [dbo].[tb_conteudo_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_conteudo_transporte_tb_transporte1] FOREIGN KEY([tb_transporte_idTransp])
REFERENCES [dbo].[tb_transporte] ([idTransp])
GO
ALTER TABLE [dbo].[tb_conteudo_transporte] CHECK CONSTRAINT [fk_tb_conteudo_transporte_tb_transporte1]
GO
ALTER TABLE [dbo].[tb_endereco]  WITH CHECK ADD  CONSTRAINT [FK_tb_endereco_tb_Cidade_idCid] FOREIGN KEY([tb_Cidade_idCid])
REFERENCES [dbo].[tb_cidade] ([idCid])
GO
ALTER TABLE [dbo].[tb_endereco] CHECK CONSTRAINT [FK_tb_endereco_tb_Cidade_idCid]
GO
ALTER TABLE [dbo].[tb_endereco]  WITH CHECK ADD  CONSTRAINT [FK_tb_endereco_tb_Estado_idEst] FOREIGN KEY([tb_Estado_idEst])
REFERENCES [dbo].[tb_estado] ([idEst])
GO
ALTER TABLE [dbo].[tb_endereco] CHECK CONSTRAINT [FK_tb_endereco_tb_Estado_idEst]
GO
ALTER TABLE [dbo].[tb_endereco_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_endereco_transporte_tb_cidadedestino] FOREIGN KEY([tb_cidadeDestino_IdCid])
REFERENCES [dbo].[tb_cidade] ([idCid])
GO
ALTER TABLE [dbo].[tb_endereco_transporte] CHECK CONSTRAINT [fk_tb_endereco_transporte_tb_cidadedestino]
GO
ALTER TABLE [dbo].[tb_endereco_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_endereco_transporte_tb_cidadeorigem] FOREIGN KEY([tb_cidadeOrigem_IdCid])
REFERENCES [dbo].[tb_cidade] ([idCid])
GO
ALTER TABLE [dbo].[tb_endereco_transporte] CHECK CONSTRAINT [fk_tb_endereco_transporte_tb_cidadeorigem]
GO
ALTER TABLE [dbo].[tb_estado]  WITH CHECK ADD  CONSTRAINT [fk_tb_estado_tb_pais] FOREIGN KEY([tb_Pais_idPais])
REFERENCES [dbo].[tb_pais] ([idPais])
GO
ALTER TABLE [dbo].[tb_estado] CHECK CONSTRAINT [fk_tb_estado_tb_pais]
GO
ALTER TABLE [dbo].[tb_imagens_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_imagens_transporte_tb_transporte1] FOREIGN KEY([tb_transporte_idTransp])
REFERENCES [dbo].[tb_transporte] ([idTransp])
GO
ALTER TABLE [dbo].[tb_imagens_transporte] CHECK CONSTRAINT [fk_tb_imagens_transporte_tb_transporte1]
GO
ALTER TABLE [dbo].[tb_item]  WITH CHECK ADD  CONSTRAINT [fk_tb_item_tb_categoria] FOREIGN KEY([tb_categoria_idCat])
REFERENCES [dbo].[tb_categoria] ([idCat])
GO
ALTER TABLE [dbo].[tb_item] CHECK CONSTRAINT [fk_tb_item_tb_categoria]
GO
ALTER TABLE [dbo].[tb_lance]  WITH CHECK ADD  CONSTRAINT [fk_tb_lance_tb_pessoa1] FOREIGN KEY([tb_pessoa_idPes])
REFERENCES [dbo].[tb_pessoa] ([idPes])
GO
ALTER TABLE [dbo].[tb_lance] CHECK CONSTRAINT [fk_tb_lance_tb_pessoa1]
GO
ALTER TABLE [dbo].[tb_lance]  WITH CHECK ADD  CONSTRAINT [fk_tb_lance_tb_transporte1] FOREIGN KEY([tb_transporte_idTransp])
REFERENCES [dbo].[tb_transporte] ([idTransp])
GO
ALTER TABLE [dbo].[tb_lance] CHECK CONSTRAINT [fk_tb_lance_tb_transporte1]
GO
ALTER TABLE [dbo].[tb_log]  WITH CHECK ADD  CONSTRAINT [fk_tb_log_tb_pessoa1] FOREIGN KEY([tb_pessoa_idPes])
REFERENCES [dbo].[tb_pessoa] ([idPes])
GO
ALTER TABLE [dbo].[tb_log] CHECK CONSTRAINT [fk_tb_log_tb_pessoa1]
GO
ALTER TABLE [dbo].[tb_mensagem]  WITH CHECK ADD  CONSTRAINT [fk_tb_mensagem_tb_lance1] FOREIGN KEY([tb_lance_idLan])
REFERENCES [dbo].[tb_lance] ([idLan])
GO
ALTER TABLE [dbo].[tb_mensagem] CHECK CONSTRAINT [fk_tb_mensagem_tb_lance1]
GO
ALTER TABLE [dbo].[tb_mensagem]  WITH CHECK ADD  CONSTRAINT [fk_tb_mensagem_tb_pessoa1] FOREIGN KEY([tb_pessoa_idPes])
REFERENCES [dbo].[tb_pessoa] ([idPes])
GO
ALTER TABLE [dbo].[tb_mensagem] CHECK CONSTRAINT [fk_tb_mensagem_tb_pessoa1]
GO
ALTER TABLE [dbo].[tb_mensalidade]  WITH CHECK ADD  CONSTRAINT [fk_tb_mensalidade_tb_pessoa] FOREIGN KEY([tb_pessoa_idPes])
REFERENCES [dbo].[tb_pessoa] ([idPes])
GO
ALTER TABLE [dbo].[tb_mensalidade] CHECK CONSTRAINT [fk_tb_mensalidade_tb_pessoa]
GO
ALTER TABLE [dbo].[tb_mensalidade]  WITH CHECK ADD  CONSTRAINT [FK_tb_mensalidade_tb_situacaoMensalidade_idSit] FOREIGN KEY([tb_situacaoMensalidade_idSit])
REFERENCES [dbo].[tb_situacaoMensalidade] ([idSit])
GO
ALTER TABLE [dbo].[tb_mensalidade] CHECK CONSTRAINT [FK_tb_mensalidade_tb_situacaoMensalidade_idSit]
GO
ALTER TABLE [dbo].[tb_pessoa]  WITH CHECK ADD  CONSTRAINT [FK_Pessoa_tb_Status_idsta] FOREIGN KEY([tb_Status_idSta])
REFERENCES [dbo].[tb_status] ([idSta])
GO
ALTER TABLE [dbo].[tb_pessoa] CHECK CONSTRAINT [FK_Pessoa_tb_Status_idsta]
GO
ALTER TABLE [dbo].[tb_pessoa]  WITH CHECK ADD  CONSTRAINT [fk_tb_pessoa_tb_endereco1] FOREIGN KEY([tb_endereco_idEnd])
REFERENCES [dbo].[tb_endereco] ([idEnd])
GO
ALTER TABLE [dbo].[tb_pessoa] CHECK CONSTRAINT [fk_tb_pessoa_tb_endereco1]
GO
ALTER TABLE [dbo].[tb_pessoa]  WITH CHECK ADD  CONSTRAINT [fk_tb_pessoa_tb_perfil1] FOREIGN KEY([tb_perfil_idPer])
REFERENCES [dbo].[tb_perfil] ([idPer])
GO
ALTER TABLE [dbo].[tb_pessoa] CHECK CONSTRAINT [fk_tb_pessoa_tb_perfil1]
GO
ALTER TABLE [dbo].[tb_pessoa_voucher]  WITH CHECK ADD  CONSTRAINT [fk_tb_pessoa_voucher_tb_pessoa1] FOREIGN KEY([tb_pessoa_idPes])
REFERENCES [dbo].[tb_pessoa] ([idPes])
GO
ALTER TABLE [dbo].[tb_pessoa_voucher] CHECK CONSTRAINT [fk_tb_pessoa_voucher_tb_pessoa1]
GO
ALTER TABLE [dbo].[tb_pessoa_voucher]  WITH CHECK ADD  CONSTRAINT [fk_tb_pessoa_voucher_tb_voucher1] FOREIGN KEY([tb_voucher_idVou])
REFERENCES [dbo].[tb_voucher] ([idVou])
GO
ALTER TABLE [dbo].[tb_pessoa_voucher] CHECK CONSTRAINT [fk_tb_pessoa_voucher_tb_voucher1]
GO
ALTER TABLE [dbo].[tb_resposta_pesquisa]  WITH CHECK ADD  CONSTRAINT [fk_resposta_pesquisa_tb_pergunta_pesquisa1] FOREIGN KEY([tb_pergunta_pesquisa_idPqa])
REFERENCES [dbo].[tb_pergunta_pesquisa] ([idPqa])
GO
ALTER TABLE [dbo].[tb_resposta_pesquisa] CHECK CONSTRAINT [fk_resposta_pesquisa_tb_pergunta_pesquisa1]
GO
ALTER TABLE [dbo].[tb_resposta_pesquisa]  WITH CHECK ADD  CONSTRAINT [fk_resposta_pesquisa_tb_pessoa1] FOREIGN KEY([tb_pessoa_idPes])
REFERENCES [dbo].[tb_pessoa] ([idPes])
GO
ALTER TABLE [dbo].[tb_resposta_pesquisa] CHECK CONSTRAINT [fk_resposta_pesquisa_tb_pessoa1]
GO
ALTER TABLE [dbo].[tb_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_statusTransp_idStaTransp] FOREIGN KEY([tb_statusTransp_idStaTransp])
REFERENCES [dbo].[tb_statusTransp] ([idStaTransp])
GO
ALTER TABLE [dbo].[tb_transporte] CHECK CONSTRAINT [fk_tb_statusTransp_idStaTransp]
GO
ALTER TABLE [dbo].[tb_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_transporte_tb_categoria1] FOREIGN KEY([tb_categoria_idCat])
REFERENCES [dbo].[tb_categoria] ([idCat])
GO
ALTER TABLE [dbo].[tb_transporte] CHECK CONSTRAINT [fk_tb_transporte_tb_categoria1]
GO
ALTER TABLE [dbo].[tb_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_transporte_tb_endereco_transporte1] FOREIGN KEY([tb_endereco_transporte_idEndTran])
REFERENCES [dbo].[tb_endereco_transporte] ([idEndTran])
GO
ALTER TABLE [dbo].[tb_transporte] CHECK CONSTRAINT [fk_tb_transporte_tb_endereco_transporte1]
GO
ALTER TABLE [dbo].[tb_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_transporte_tb_pessoa1] FOREIGN KEY([tb_pessoa_idPes])
REFERENCES [dbo].[tb_pessoa] ([idPes])
GO
ALTER TABLE [dbo].[tb_transporte] CHECK CONSTRAINT [fk_tb_transporte_tb_pessoa1]
GO
