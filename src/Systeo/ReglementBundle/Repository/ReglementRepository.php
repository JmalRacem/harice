<?php

namespace Systeo\ReglementBundle\Repository;

use Doctrine\ORM\QueryBuilder;
/**
 * ReglementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReglementRepository extends \Doctrine\ORM\EntityRepository
{
    public function MyFindAllOut($data)
   {
       $queryBuilder = $this->createQueryBuilder('a');
       
       $queryBuilder->leftJoin('a.tier', 'tier')
           ->addSelect('tier');
       $queryBuilder->leftJoin('a.depense', 'depense')
           ->addSelect('depense');
       
       
       $this->searchDirection($queryBuilder,$data);
       $this->searchName($queryBuilder,$data);
       $this->searchTier($queryBuilder,$data);
       $this->searchType($queryBuilder,$data);
       $this->searchDepense($queryBuilder,$data);
       $this->searchMontant($queryBuilder, $data);
       $this->searchDate($queryBuilder, $data);
      
       $queryBuilder->orderBy('a.date','DESC');
       
       return $queryBuilder;
   }
   
   public function getSumOperationsOut($data){
       $querySum = $this->createQueryBuilder('a');
       
        $querySum->leftJoin('a.tier', 'tier')
           ->addSelect('tier');
        $querySum->leftJoin('a.depense', 'depense')
           ->addSelect('depense');
       
       $querySum->select('SUM(a.montant) as montant');
       
       $this->searchDirection($querySum,$data);
       $this->searchName($querySum,$data);
       $this->searchTier($querySum,$data);
       $this->searchType($querySum,$data);
       $this->searchDepense($querySum,$data);
       $this->searchMontant($querySum, $data);
       $this->searchDate($querySum, $data);
       
       $result = $querySum->getQuery()->execute();
       
       return $result[0];
   }
   /**
    * 
    * @param string $entity
    * @param int $entity_id
    */
   public function removeRelatedEntity($entity,$entity_id){
       
       $query = $this->createQueryBuilder('a');
       $query->update()
             ->set('a.'.$entity,':entity')
             ->where('a.'.$entity .'=  :'.$entity)
             ->setParameter('entity', null)
             ->setParameter($entity, $entity_id);
       
       $query->getQuery()->execute();
       
   }
   
   /**
    * 
    * @param type $name
    */
   private function searchName(QueryBuilder $qb, $data)
   {
       if(isset($data['name']) && !empty($data['name'])){
           $qb->andWhere('a.name like :name')
              ->setParameter('name', '%'.$data['name'].'%');
       }
   }
   
   /**
    * 
    * @param type $name
    */
   private function searchDepense(QueryBuilder $qb, $data)
   {
       if(isset($data['depense']) && !empty($data['depense'])){
           $qb->andWhere('depense.name like :depense')
              ->setParameter('depense', '%'.$data['depense'].'%');
       }
   }
   
   /**
    * 
    * @param type $name
    */
   private function searchTier(QueryBuilder $qb, $data)
   {
       if(isset($data['tier']) && !empty($data['tier'])){
           $qb->andWhere('tier.rs like :tier OR tier.firstName like :tier OR tier.lastName like :tier')
           
              ->setParameter('tier', '%'.$data['tier'].'%');
       }
   }
   
   /**
    * 
    * @param type $name
    */
   private function searchDirection(QueryBuilder $qb, $data)
   {
       if(isset($data['direction']) && !empty($data['direction'])){
           $qb->andWhere('a.direction = :direction')
           
              ->setParameter('direction', $data['direction']);
       }
   }
   
   /**
    * 
    * @param type $name
    */
   private function searchType(QueryBuilder $qb, $data)
   {
       if(isset($data['type']) && !empty($data['type'])){
           $qb->andWhere('a.type = :type')
           
              ->setParameter('type', $data['type']);
       }
   }
   
   
   /**
    * 
    * @param type $name
    */
   private function searchMontant(QueryBuilder $qb, $data)
   {
       if(isset($data['montant']) && !empty($data['montant']) && isset($data['montant_comparateur']) && !empty($data['montant_comparateur'])){
           $qb->andWhere('a.montant '.$data['montant_comparateur'].' :montant')
              ->setParameter('montant', $data['montant']);
       }
   }
   
  
   
   private function searchDate(QueryBuilder $qb, $data) {

        $date_1 = false;
        $date_2 = false;

        if (isset($data['date_debut']) && !empty($data['date_debut'])) {
            $date_1 = true;
        }

        if (isset($data['date_fin']) && !empty($data['date_fin'])) {
            $date_2 = true;
        }

        if ($date_1 && !$date_2) {
            $qb->andWhere('a.date >= :date')
                    ->setParameter('date', $data['date_debut']);
        } elseif (!$date_1 && $date_2) {
            $qb->andWhere('a.date <= :date')
                    ->setParameter('date', $data['date_fin']);
        } elseif ($date_1 && $date_2) {
            $qb->andWhere('a.date BETWEEN :start AND :end')
                    ->setParameter('start', $data['date_debut'])
                    ->setParameter('end', $data['date_fin'])
            ;
        }
    }
    
   
}