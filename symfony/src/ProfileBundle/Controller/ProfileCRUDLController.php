<?php

namespace App\ProfileBundle\Controller;

use App\ProfileBundle\Repository\ProfileRepository;
use App\ProfileBundle\Service\ProfileService;
use App\ProfileBundle\Entity\Profile;
use App\ProfileBundle\Event\ProfileEvent;
use App\ProfileBundle\Form\ProfileType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\ProfileBundle\Events as ProfileEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/profiles")
 */
class ProfileCRUDLController extends CRUDLController
{
    /**
     * @var ProfileService
     */
    private $profileService;

    /**
     * ProfileCRUDLController constructor.
     *
     * @param ProfileService $profileService
     */
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        return $this->render(
            'index.html.twig',
            ['profiles' => $this->getEntityRepository()->findAllProfiles($request->query->get('page', 1))]
        );
    }

    /**
     * Метод для поиска, сортировки
     * Ищет как в Elastic так и Mysql
     * пример для Elastic
     * search?query=99999999999999999999999999
     * пример для Mysql
     * search?query=99999999999999999999999999&queryBy=phonenumber&orderBy=firstname&order=desc&db=mysql
     *
     * По Elastic сортировки не работают, только точное совпадение.
     * По Mysql поиск строки в подстроке.
     */
    public function searchAction(Request $request): Response
    {
        return $this->render('index.html.twig', ['profiles' => $this->searchProfiles($request)]);
    }

    /**
     * TODO метод должен находится в провайдере, но.. комментарий ниже
     * @param Request $request
     *
     * @return Pagerfanta
     */
    private function searchProfiles(Request $request)
    {
        $db = $request->query->get('db', 'elastic');

        if ($db === 'elastic') {
            $profiles = $this->findBySearchQueryInElastic($request);
        } else {
            $profiles = $this->getEntityRepository()->findBySearchQueryInMysql($request);
        }

        return $profiles;
    }

    /**
     * @Route(path="/{id}")
     * @Method({"GET"})
     *
     * @param Request $request
     * @param         $id
     *
     * @return Response
     */
    public function readAction(Request $request, $id)
    {
        return new Response(
            $this->render(
                'profile/index.html.twig',
                [
                    'profiles' => $this->getEntityRepository()->find($id),
                ]
            )
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createEntityForm();

        $form->handleRequest($request);

        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $profileData = $form->getData();

            $result = $this->profileService->setProfile($profileData);
        }

        return new Response(json_encode($result));
    }

    /**
     * Get repository of the Entity
     *
     */
    protected function getEntityRepository()
    {
        return $this->getDoctrine()->getRepository(Profile::class);
    }

    /**
     * @param object $entity
     * @param array  $options
     *
     * @return FormInterface
     */
    protected function createEntityForm($entity = null, array $options = []): FormInterface
    {
        return $this->createForm(ProfileType::class, $entity, $options);
    }

    /**
     * @param Profile $Profile
     */
    protected function onCreateSuccess($Profile)
    {
        $this->get('event_dispatcher')->dispatch(ProfileEvents::CREATE, new ProfileEvent($Profile));
    }

    /**
     * @param Profile $Profile
     */
    protected function onDeleteSuccess($Profile)
    {
        $this->get('event_dispatcher')->dispatch(ProfileEvents::DELETE, new ProfileEvent($Profile));
    }

    /**
     * @inheritDoc
     */
    protected function getCacheOptions(): array
    {
        return [
            'public' => true,
            's_maxage' => 60,
            'max_age' => 60,
        ];
    }

    /**
     * TODO метод находиться здесь, потому что fos_elastica не адаптированна для symfony 4.
     * А инжектить целый контейнер в провайдер - это плохо.
     * По хорошему, для подобных бандлов нужно писать адаптеры.
     *
     * @param Request $request
     *
     * @return Pagerfanta
     */
    private function findBySearchQueryInElastic(Request $request)
    {
        $query = $request->query->get('query', '');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10) <= ProfileRepository::MAX_PER_PAGE
            ? $request->query->get('l', 10) : ProfileRepository::MAX_PER_PAGE;

        $finder = $this->get('fos_elastica.finder.app.user');
        $results = $finder->find($query, ProfileRepository::MAX_FIND_ELASTICA_RESULT);

        $adapter = new ArrayAdapter($results);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }
}
