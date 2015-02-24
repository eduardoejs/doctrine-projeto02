<?php

namespace EJS\Produtos\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ProdutoRepository extends EntityRepository{

    //QueryBuilder
    public function getProdutosOrdenados(){
        return $this
            ->createQueryBuilder("p")
            ->orderBy("p.nome", "asc")
            ->getQuery()
            ->getResult();
    }

    //DQL - Doctrine Query Language
    public function getProdutosDesc()
    {
        $dql = "SELECT p FROM EJS\Produtos\Entity\Produto p order by p.nome desc";
        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getResult();
    }

    public function pesquisarProdutos($nome){
        $dql = "SELECT p FROM EJS\Produtos\Entity\Produto p WHERE p.nome LIKE :campoPesquisa";
        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter('campoPesquisa', "%{$nome}%")
            ->getResult();
    }

    public function paginarRegistros($qtdePaginas, $paginaAtual){
        $dql = "SELECT p FROM EJS\Produtos\Entity\Produto p";
        $resultQuery = $this
                        ->getEntityManager()
                        ->createQuery($dql)
                        ->setFirstResult($qtdePaginas * ($paginaAtual-1))
                        ->setMaxResults($qtdePaginas);
        $pagina = new Paginator($resultQuery, $fetchJoinCollection = true);

        return $pagina;
    }
} 