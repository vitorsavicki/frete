USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_transporte]    Script Date: 08/06/2015 10:21:06 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[tb_transporte](
	[idTransp] [int] IDENTITY(1,1) NOT NULL,
	[descricaoTransp] [varchar](255) NOT NULL,
	[dataRetiradaTransp] [datetime] NULL,
	[dataCadastroTransp] [datetime] NOT NULL,
	[statusTransp] [varchar](255) NOT NULL,
	[comentarioAdiTransp] [varchar](500) NULL,
	[numAjudantesTransp] [int] NULL,
	[tb_categoria_idCat] [int] NOT NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
	[tb_endereco_transporte_idEndTran] [int] NOT NULL,
	[precoMaxiTransp] [decimal](9, 3) NULL,
PRIMARY KEY CLUSTERED 
(
	[idTransp] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
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


