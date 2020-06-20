USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_mensagem]    Script Date: 08/06/2015 10:20:50 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[tb_mensagem](
	[idMen] [int] IDENTITY(1,1) NOT NULL,
	[remententeMen] [varchar](255) NOT NULL,
	[destinatarioMen] [varchar](255) NOT NULL,
	[dataRecebidaMen] [datetime] NOT NULL,
	[dataEnvioMen] [datetime] NOT NULL,
	[assuntoMen] [varchar](255) NOT NULL,
	[descricaoMen] [varchar](255) NOT NULL,
	[tb_transporte_idTransp] [int] NOT NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idMen] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

ALTER TABLE [dbo].[tb_mensagem]  WITH CHECK ADD  CONSTRAINT [fk_tb_mensagem_tb_pessoa1] FOREIGN KEY([tb_pessoa_idPes])
REFERENCES [dbo].[tb_pessoa] ([idPes])
GO

ALTER TABLE [dbo].[tb_mensagem] CHECK CONSTRAINT [fk_tb_mensagem_tb_pessoa1]
GO

ALTER TABLE [dbo].[tb_mensagem]  WITH CHECK ADD  CONSTRAINT [fk_tb_mensagem_tb_transporte1] FOREIGN KEY([tb_transporte_idTransp])
REFERENCES [dbo].[tb_transporte] ([idTransp])
GO

ALTER TABLE [dbo].[tb_mensagem] CHECK CONSTRAINT [fk_tb_mensagem_tb_transporte1]
GO


