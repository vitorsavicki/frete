USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_imagens_transporte]    Script Date: 08/06/2015 10:21:54 ******/
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

ALTER TABLE [dbo].[tb_imagens_transporte]  WITH CHECK ADD  CONSTRAINT [fk_tb_imagens_transporte_tb_transporte1] FOREIGN KEY([tb_transporte_idTransp])
REFERENCES [dbo].[tb_transporte] ([idTransp])
GO

ALTER TABLE [dbo].[tb_imagens_transporte] CHECK CONSTRAINT [fk_tb_imagens_transporte_tb_transporte1]
GO


