USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_endereco_transporte]    Script Date: 08/06/2015 10:21:39 ******/
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


