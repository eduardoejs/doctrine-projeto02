<?php

namespace EJS\Produtos\Controller;

use EJS\Produtos\Entity\Produto;
use EJS\Produtos\Service\ProdutoService;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;


class ProdutoController implements ControllerProviderInterface {

    private $produto;

    public function connect(Application $app)
    {
        $produtoController = $app['controllers_factory'];

        //APIs Publicas
        //API para listar todos os produtos
        $app->get("/api/produtos", function() use($app){
           $dados = $app['produtoService']->listProdutos();
           return $app->json($dados);
        })->bind('API-ListProdutos');

        //API para listar 1 registro apenas
        $app->get("/api/produtos/{id}", function($id) use($app){
           $dados = $app['produtoService']->listProdutoById($id);
           if($dados){
               return $app->json($dados);
           }else{
               return $app->json(['ERRO' => 'Não foi possível exibir o registro']);
           }
        });

        //API para inserir novo registro
        $app->post("/api/produtos", function(Request $request) use($app){
            $dados['nome'] = $request->get('nome');
            $dados['descricao'] = $request->get('descricao');
            $dados['valor'] = $request->get('valor');

            $result = $app['produtoService']->insertProduto($dados);
            return $app->json($result);
        });

        //API para alterar um registro
        $app->put("/api/produtos/{id}", function($id, Request $request) use($app){
            $dados['id'] =  $id;
            $dados['nome'] = $request->request->get('nome');
            $dados['descricao'] = $request->request->get('descricao');
            $dados['valor'] = $request->request->get('valor');

            $result = $app['produtoService']->alterarProduto($dados);
            return $app->json($result);
        });

        //API para remover um registro
        $app->delete("/api/produtos/{id}", function($id) use($app){

            $dados = $app['produtoService']->listProdutoById($id);

            if($dados){
                if($app['produtoService']->deleteProduto($id)){
                    return $app->json(['SUCCESSO' => 'Registro excluído com sucesso']);
                }else{
                    return $app->json(['ERRO' => 'Não foi possível excluir o registro']);
                }
            }else{
                return $app->json(['ERRO' => 'Registro não encontrado']);
            }
        });

        //fim APIs

        //Rota: index(listagem de produtos)
        $app->get('/', function() use ($app){
            //return $app['twig']->render('index.twig', []);
            $pagina = 1;
            $produtos = $app['produtoService']->paginacao(5, $pagina);
            return $app['twig']->render('conteudo.twig', ['produtos' => $produtos, 'data' => $produtos->count()]);
        })->bind('index');

        //Rota: listar produto por ID
        $app->get('/produto/view/{id}', function($id) use($app){
            $produto = new Produto();
            $data['nome'] = $produto->getNome();
            $data['descricao'] = $produto->getDescricao();
            $data['valor'] = $produto->getValor();

            $result = $app['produtoService']->listProdutoById($id);

            return $app['twig']->render('visualizar.twig', ['produto' => $result]);
        })->bind('visualizar');

        //Rota para o formulário de insert
        $app->get('/produto/novo', function() use($app){
            return $app['twig']->render('novo.twig',[]);
        })->bind('novo');

        //Rota: após pegar dados do formulário insere no banco de dados
        $app->post('/inserir', function(Request $request) use($app){
            $data = $request->request->all();
            $produto = new Produto();
            $produto->setNome($data['nome']);
            $produto->setValor($data['valor']);

            $result = $app['produtoService']->insertProduto($data);
            return $app['twig']->render('status_insert.twig', ['status' => $result]);
        })->bind('inserir');

        //Rota: mensagem de sucesso ao inserir novo registro [utilizar no metodo redirect->generate]
        $app->get('/sucesso', function () use ($app) {
            return $app['twig']->render('sucesso.twig', []);
        })->bind("sucesso");

        //Rota: formulário de alteração
        $app->get('/produto/alterar/{id}', function($id) use($app){
            $produto = new Produto();
            $data['nome'] = $produto->getNome();
            $data['descricao'] = $produto->getDescricao();
            $data['valor'] = $produto->getValor();
            $result = $app['produtoService']->listProdutoById($id);

            return $app['twig']->render('alterar.twig', ['produto' => $result]);
        })->bind('alterar');

        //Rota para alterar registro
        $app->post('/alterar', function(Request $request) use($app){
            $data = $request->request->all();
            $produto = new Produto();
            $produto->setNome($data['nome']);
            $produto->setDescricao($data['descricao']);
            $produto->setValor($data['valor']);
            $produto->setId($data['id']);

            $result = $app['produtoService']->alterarProduto($data);
            return $app['twig']->render('status_update.twig', ['status' => $result, 'produto' => $produto]);
        })->bind('update');

        //Rota para excluir registro
        $app->get('/produto/delete/{id}', function($id) use($app){
            if($app['produtoService']->deleteProduto($id)){
                return $app->redirect($app['url_generator']->generate('index'));
            }else{
                $app->abort(500, "Erro ao excluir produto");
            }
        })->bind('excluir');

        //rota para paginacao
        $app->get('/produtosPaginado/{pagina}', function($pagina) use($app){
            $produtos = $app['produtoService']->paginacao(5, $pagina);
            return $app['twig']->render('conteudo.twig', ['produtos' => $produtos, 'data' => $produtos->count()]);
        })->bind('listar_produtos_paginado');

        //rota para pesquisar um produto
        $app->post('/produto/pesquisar', function(Request $request) use($app){

            $data = $request->request->all();

            $produto = new Produto();
            $produto->setNome($data['nome']);

            $result = $app['produtoService']->pesquisarProduto($produto->getNome());

            if(empty($result)){
                return $app['twig']->render('404produto.twig', [
                    'mensagem' => 'Nenhum produto encontrado!',
                ]);
            }
            return $app['twig']->render('pesquisa_produtos.twig', ['produtos' => $result]);
        })->bind('pesquisar');

        return $produtoController;
    }
} 