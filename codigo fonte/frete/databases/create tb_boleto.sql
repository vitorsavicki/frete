USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_boleto]    Script Date: 27/05/2015 11:41:11 ******/
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

ALTER TABLE [dbo].[tb_boleto] ADD  DEFAULT ('R$') FOR [especieBol]
GO

ALTER TABLE [dbo].[tb_boleto] ADD  DEFAULT ('CNR') FOR [carteiraBol]
GO

ALTER TABLE [dbo].[tb_boleto]  WITH CHECK ADD  CONSTRAINT [FK_tb_boleto_tb_mensalidade] FOREIGN KEY([idMensa])
REFERENCES [dbo].[tb_mensalidade] ([idMensa])
GO

ALTER TABLE [dbo].[tb_boleto] CHECK CONSTRAINT [FK_tb_boleto_tb_mensalidade]
GO


