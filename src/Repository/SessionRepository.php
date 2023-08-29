<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /** Afficher les stagiaires non inscrits */
    public function findNonInscrits($session_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre
        $qb->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions', 'se')
            ->where('se.id = :id');
        
        $sub = $em->createQueryBuilder();
        // sélectionner tous les stagiaires qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les stagiaires non inscrits pour une session définie
        $sub->select('st')
            ->from('App\Entity\Stagiaire', 'st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
            // requête paramétrée
            ->setParameter('id', $session_id)
            // trier la liste des stagiaires sur le nom de famille
            ->orderBy('st.nom');
        
        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }

    /*récupérer les modules non programmés dans une session donnée*/
    public function findNonProgramme($session_id){
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les modules d'une session dont l'id est passé en paramètre
        $qb->select('p')
            ->from('App\Entity\Programme', 'p')
            ->leftJoin('p.session', 's')
            ->where('s.id = :id');

        $sub = $em->createQueryBuilder();
        // sélectionner tous les modules qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les modules non  pour une session définie
        $sub->select('po')
            ->from('App\Entity\Programme', 'po')
            ->where($sub->expr()->notIn('po.id', $qb->getDQL()))
            // requête paramétrée
            ->setParameter('id', $session_id);
    
        // renvoyer le résultat
        $query = $sub->getQuery();

        return $query->getResult();
    }

    public function findPastSessions(){             //method to get finished sessions
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('s')
            ->from('App\Entity\Session', 's')
            ->where('s.dateFin < CURRENT_TIMESTAMP()')
            ->orderBy('s.dateDebut');
        
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findFutureSessions(){           //method to get future sessions
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

   
        $qb->select('s')
            ->from('App\Entity\Session', 's')
            ->where('s.dateDebut > CURRENT_TIMESTAMP()')
            ->orderBy('s.dateDebut');
        
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findCurrentSessions(){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('s')
            ->from('App\Entity\Session', 's')
            ->where('s.dateDebut < CURRENT_TIMESTAMP()')
            ->andWhere('s.dateFin > CURRENT_TIMESTAMP()')
            ->orderBy('s.dateDebut');
        
        $query = $qb->getQuery();
        return $query->getResult();
    }
}