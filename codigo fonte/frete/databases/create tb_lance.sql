USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_lance]    Script Date: 08/06/2015 10:19:23 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[tb_lance](
	[idLan] [int] IDENTITY(1,1) NOT NULL,
	[valorLan] [decimal](9, 3) NULL,
	[dataLan] [datetime] NULL,
	[tb_transporte_idTransp] [int] NOT NULL,
	[tb_pessoa_idPes] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idLan] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

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


