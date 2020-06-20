USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_conteudo_transporte]    Script Date: 08/06/2015 10:21:22 ******/
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


