USE [freteimediato]
GO

/****** Object:  Table [dbo].[tb_estado]    Script Date: 06/06/2015 17:16:54 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[tb_estado](
	[idEst] [int] IDENTITY(1,1) NOT NULL,
	[nomeEst] [varchar](75) NOT NULL,
	[ufEst] [varchar](5) NOT NULL,
	[tb_Pais_idPais] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[idEst] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

ALTER TABLE [dbo].[tb_estado]  WITH CHECK ADD  CONSTRAINT [fk_tb_estado_tb_pais] FOREIGN KEY([tb_Pais_idPais])
REFERENCES [dbo].[tb_pais] ([idPais])
GO

SET ANSI_PADDING OFF
GO

INSERT INTO [dbo].[tb_estado] VALUES
('Acre', 'AC', 1),
('Alagoas', 'AL', 1),
('Amazonas', 'AM', 1),
('Amap�', 'AP', 1),
('Bahia', 'BA', 1),
('Cear�', 'CE', 1),
('Distrito Federal', 'DF', 1),
('Esp�rito Santo', 'ES', 1),
('Goi�s', 'GO', 1),
('Maranh�o', 'MA', 1),
('Minas Gerais', 'MG', 1),
('Mato Grosso do Sul', 'MS', 1),
('Mato Grosso', 'MT', 1),
('Par�', 'PA', 1),
('Para�ba', 'PB', 1),
('Pernambuco', 'PE', 1),
('Piau�', 'PI', 1),
('Paran�', 'PR', 1),
('Rio de Janeiro', 'RJ', 1),
('Rio Grande do Norte', 'RN', 1),
('Rond�nia', 'RO', 1),
('Roraima', 'RR', 1),
('Rio Grande do Sul', 'RS', 1),
('Santa Catarina', 'SC', 1),
('Sergipe', 'SE', 1),
('S�o Paulo', 'SP', 1),
('Tocantins', 'TO', 1);