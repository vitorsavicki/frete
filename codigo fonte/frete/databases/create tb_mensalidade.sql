USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_mensalidade]    Script Date: 27/05/2015 11:42:23 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[tb_mensalidade](
	[idMensa] [int] IDENTITY(1,1) NOT NULL,
	[dataVencimentoMensa] [datetime] NULL,
	[dataPagamentoMensa] [datetime] NULL,
	[valorMensa] [decimal](9, 3) NULL,
	[tb_situacaoMensa_idSit] [int] NOT NULL,
	[tb_transportador_idTran] [int] NOT NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idMensa] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

ALTER TABLE [dbo].[tb_mensalidade]  WITH CHECK ADD  CONSTRAINT [FK_tb_mensalidade_tb_situacaomensalidade] FOREIGN KEY([idMensa])
REFERENCES [dbo].[tb_situacaoMensalidade] ([idSit])
GO

ALTER TABLE [dbo].[tb_mensalidade] CHECK CONSTRAINT [FK_tb_mensalidade_tb_situacaomensalidade]
GO


