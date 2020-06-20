USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_categoria]    Script Date: 06/06/2015 17:16:54 ******/
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

INSERT INTO [dbo].[tb_pais] VALUES ('Brasil', 'BR');