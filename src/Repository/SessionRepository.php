<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /** Get number of stagaires that are part of a session */
    public function findInscrits($session_id)
    {
        $em = $this->getEntityManager();
        $qb= $em->createQueryBuilder();
        
        $qb->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions', 'se')                              //select all stagiaires that are part of the session
            ->where('se.id = :id')
            ->setParameter('id', $session_id)                           //set parameter
            ->orderBy('s.nom');   
        
        $query = $qb->getQuery();                                       //return result
        return $query->getResult();
    }


    /** Get stagaires that are not part of a session */
    public function findNonInscrits($session_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        
        $qb->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions', 'se')                              //select all stagiaires that are part of the session
            ->where('se.id = :id'); 
        
        $sub = $em->createQueryBuilder();
        
        $sub->select('st')
            ->from('App\Entity\Stagiaire', 'st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))        //select all stagiaires that are NOT IN previous query
            ->setParameter('id', $session_id)                           //we get all stagiaires that are not part of the session
            ->orderBy('st.nom');                                        //sort by name
        
        $query = $sub->getQuery();                                       //return result
        return $query->getResult();
    }


    /** Method to get all finished sessions **/
    public function findPastSessions(){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('s')
            ->from('App\Entity\Session', 's')
            ->where('s.dateFin < CURRENT_TIMESTAMP()')                     //select all sessions with an endDate that has already past
            ->orderBy('s.dateDebut');
        
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /** Method to get all future sessions **/
    public function findFutureSessions(){ 
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

   
        $qb->select('s')
            ->from('App\Entity\Session', 's')
            ->where('s.dateDebut > CURRENT_TIMESTAMP()')                    //select all sessions with a startDate that is in the future
            ->orderBy('s.dateDebut');
        
        $query = $qb->getQuery();
        return $query->getResult();
    }


    /** Method to get all current sessions **/
    public function findCurrentSessions(){        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('s')
            ->from('App\Entity\Session', 's')
            ->where('s.dateDebut < CURRENT_TIMESTAMP()')                    //select all sessions with a startDate that ihas already past
            ->andWhere('s.dateFin > CURRENT_TIMESTAMP()')                   //and an endDate that is in the future
            ->orderBy('s.dateDebut');
        
        $query = $qb->getQuery();
        return $query->getResult();
    }


    /** Get modules that are not part of a session **/
    public function findNonProgramme($session_id){
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        $qb->select('m')
            ->from('App\Entity\Module', 'm')
            ->leftJoin('m.programmes', 'p')
            ->where('p.session = :id');

        $sub = $em->createQueryBuilder();

        $sub->select('mo')
            ->from('App\Entity\Module', 'mo')
            ->where($sub->expr()->notIn('mo.id', $qb->getDQL()))
            ->setParameter('id', $session_id)
            ->orderBy('mo.nom');

        $query = $sub->getQuery();

        return $query->getResult();
    }
}