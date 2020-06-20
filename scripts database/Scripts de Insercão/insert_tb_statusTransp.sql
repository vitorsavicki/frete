USE [freteimediato]
GO

INSERT INTO [dbo].[tb_statusTransp]
           ([nomeStaTransp]
           ,[codigoStaTransp])
     VALUES
           ('Em andamento',
           'E'),
		   ('Concluido',
           'C'),
		   ('Cancelado pelo Cliente',
           'X'),
		    ('Aberto',
           'A')
GO


