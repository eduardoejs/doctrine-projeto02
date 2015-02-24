<?php

namespace EJS\Produtos\Service;

use Doctrine\ORM\EntityManager;
use EJS\Produtos\Entity\Produto as ProdutoEntity;

class ProdutoService {

    private $em;

    function __construct($em) {
        $this->em = $em;
    }

    public function listProdutos()
    {
        $repository = $this->em->getRepository("EJS\Produtos\Entity\Produto");
        //$result = $repository->findAll();
        $result = $repository->getProdutosOrdenados();

        $produtos = array();
        foreach($result as $produto)
        {
            $p = array();
            $p['id'] = $produto->getId();
            $p['nome'] = $produto->getNome();
            $p['descricao'] = $produto->getDescricao();
            $p['valor'] = $produto->getValor();

            $produtos[] = $p;
        }
        return $produtos;
    }

    public function listProdutoById($id){
        $repository = $this->em->getRepository("EJS\Produtos\Entity\Produto");
        $result = $repository->find($id);

        if($result != null)
        {
            $produto = array();
            $produto['id'] = $result->getId();
            $produto['nome'] = $result->getNome();
            $produto['descricao'] = $result->getDescricao();
            $produto['valor'] = $result->getValor();
            return $produto;
        }
        else{
            return false;
        }
    }

    public function insertProduto($data){

        $produtoEntity = new ProdutoEntity();
        $produtoEntity->setNome($data['nome']);
        $produtoEntity->setDescricao($data['descricao']);
        $produtoEntity->setValor($data['valor']);

        if(empty($data['nome']) or empty($data['descricao']) or empty($data['valor'])){
            return ["STATUS" => "Erro: Você deve informar todos os valores"];
        }elseif(!is_numeric($data['valor'])){
            return ["STATUS" => "O formato do campo Valor está incorreto. (Não use vírgula)"];
        }
        else{
            $this->em->persist($produtoEntity);
            $this->em->flush();
            return ["STATUS" => "Registro cadastrado com sucesso"];
        }
    }

    public function alterarProduto($data){

        $produto = $this->em->getReference("EJS\Produtos\Entity\Produto", $data['id']);

        $produto->setId($data['id']);
        $produto->setNome($data['nome']);
        $produto->setDescricao($data['descricao']);
        $produto->setValor($data['valor']);

        if(empty($data['nome']) or empty($data['descricao']) or empty($data['valor'])){
            return ["STATUS" => "Erro: Você deve informar todos os valores"];
        }elseif(!is_numeric($data['valor'])){
            return ["STATUS" => "O formato do campo Valor está incorreto. (Não use vírgula)"];
        }
        else{
            $this->em->persist($produto);
            $this->em->flush();
            return ["STATUS" => "Registro alterado com sucesso"];
        }
    }

    public function deleteProduto($id)
    {
        $produto = $this->em->getReference("EJS\Produtos\Entity\Produto", $id);
        $this->em->remove($produto);
        $this->em->flush();
        return true;
    }

    public function pesquisarProduto($nome){
        return $this->em->getRepository("EJS\Produtos\Entity\Produto")->pesquisarProdutos($nome);
    }

    public function paginacao($qtdePaginas, $paginaAtual){
        return $this->em->getRepository("EJS\Produtos\Entity\Produto")->paginarRegistros($qtdePaginas, $paginaAtual);
    }
}