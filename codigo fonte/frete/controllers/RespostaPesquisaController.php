<?php
require_once 'models/RespostaPesquisaModel.php';
include './lib/function.php';

class RespostaPesquisaController
{

    public function listarRespostaPesquisaAction()
    {
        $o_resposta_pesquisa = new RespostaPesquisaModel();
        
        //Listando as situações cadastradas
        $v_resposta_pesquisa = $o_resposta_pesquisa->_list();
        
        //definindo qual o arquivo HTML que será usado para
        //mostrar a lista de situações de mensalidade
        $o_view = new View('views/listarRespostaPesquisa.phtml');
        
        //Passando os dados da mmenalidade para a View
        $o_view->setParams(array('v_resposta_pesquisa' => $v_resposta_pesquisa));
        
        //Imprimindo código HTML
        $o_view->showContents();
    }
    
    
    /**
    * Gerencia a  de criação
    * e edição das situações de mensalidade
    */
    public function manterRespostaPesquisaAction()
    {
        $o_resposta_pesquisa = new RespostaPesquisaModel();
        
        //verificando se o id da situacao da mensalidade foi passado
        if( isset($_REQUEST['idResPes']) )
            //verificando se o id passado é valido
            if( DataValidator::isNumeric($_REQUEST['idResPes']) )
                //buscando dados do contato
                $o_resposta_pesquisa->loadById($_REQUEST['idResPes']);
            
        if(count($_POST) > 0)
        {
            $o_resposta_pesquisa->setTb_pergunta_pesquisa_idPqa(DataFilter::cleanString(protecao($_POST['tb_pergunta_pesquisa_idPqa'])));
            $o_resposta_pesquisa->setTb_pessoa_idPes(DataFilter::cleanString($_POST['tb_pessoa_idPes']));
            
            //salvando dados e redirecionando para a lista de situações de mensalidade
                $o_resposta_pesquisa->save();
                if($_REQUEST['idResPes']){
                    Application::redirect('?controle=RespostaPesquisa&acao=listarRespostaPesquisa&variavel=editada');
                    exit;
                }
                else{
                    Application::redirect('?controle=RespostaPesquisa&acao=listarRespostaPesquisa&variavel=cadastrada');
                    exit;
                }
                
            
        }
            
        $o_view = new View('views/manterRespostaPesquisa.phtml');
        $o_view->setParams(array('o_resposta_pesquisa' => $o_resposta_pesquisa));
        $o_view->showContents();
    }
    
    /**
    * Gerencia a requisições de exclusão das situações de mensalidade
    */
    public function apagarRespostaPesquisaAction()
    {
        if( DataValidator::isNumeric($_GET['idResPes']) )
        {
            //apagando a situação da mensalidade
            $o_resposta_pesquisa = new RespostaPesquisaModel();
            $o_resposta_pesquisa->loadById($_GET['idResPes']);
            
            
    
            if($o_voucher->delete()){
              Application::redirect('?controle=RespostaPesquisa&acao=listarRespostaPesquisa&variavel=excluida');
              exit;
            }
            else{
              Application::redirect('?controle=RespostaPesquisa&acao=listarRespostaPesquisa&variavel=vinculo');
              exit;
            }
            
            
        }   
    }

}
?>