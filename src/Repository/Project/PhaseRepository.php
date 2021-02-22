<?php

namespace App\Repository\Project;

use App\Entity\Project\Etude;
use App\Entity\Project\Phase;
use App\Entity\Project\ProcesVerbal;
use Doctrine\ORM\EntityRepository;

/**
 * PhaseRepository.
 */
class PhaseRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByEtudeQuery(Etude $etude)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('p')
            ->from(Phase::class, 'p')
            ->where('p.etude = :etude')
            ->setParameter('etude', $etude);

        return $qb;
    }

    /**
     * Used to display phase selector in ProcesVerbalType.
     * If a PV is set, return a query builder to all phases not associated to a PV & the ones associated with given PV
     * If no PV is set, return a query builder to all phases not associated to a PV.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByEtudeAndPv(Etude $etude, ProcesVerbal $pv = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('p')
            ->from(Phase::class, 'p')
            ->where('p.procesVerbal IS NULL');
        if (null !== $pv) {
            $qb->where('p.procesVerbal IS NULL OR p.procesVerbal = :pv_id')
            ->setParameter('pv_id', $pv->getId());
        }
        $qb->andWhere('p.etude = :etude')
            ->setParameter('etude', $etude);

        return $qb;
    }
}
