<?php
declare(strict_types=1);

namespace App\Extention;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class WalletExtention implements QueryCollectionExtensionInterface
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    public function __construct(Security $security, EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($resourceClass == Wallet::class) {
            if(is_null($this->security->getUser())) {
                return false;
            }
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return true;
            }

            $alias = $queryBuilder->getRootAliases()[0];

            if ($this->security->isGranted('ROLE_USER'))
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->in(
                        sprintf('%s.id', $alias),
                        $this->entityManager->getRepository(Wallet::class)->getWalletCollection()
                    ))
                    ->setParameter('ownerId', $this->security->getUser()->getId());
        }
    }
}
