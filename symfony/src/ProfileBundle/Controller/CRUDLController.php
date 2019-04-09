<?php

namespace App\ProfileBundle\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;

abstract class CRUDLController extends FOSRestController
{
    /**
     * Get repository of the Entity
     */
    abstract protected function getEntityRepository();

    /**
     * Create Form for the Entity
     *
     * @param object $entity
     * @param array $options
     *
     * @return FormInterface
     */
    abstract protected function createEntityForm($entity, array $options = []): FormInterface;

    /**
     * @param $entity
     */
    protected function save($entity)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param $entity
     */
    protected function onCreateSuccess($entity)
    {
        $this->save($entity);
    }

    /**
     * @return array
     */
    protected function getCacheOptions()
    {
        return [];
    }

    /**
     * @return Response
     */
    protected function applyCacheOptions(Response $response)
    {
        return $response->setCache($this->getCacheOptions());
    }
}
