<?php
/**
 * Created by IntelliJ IDEA.
 * User: meg4r0m
 * Date: 09/06/18
 * Time: 16:02
 */

namespace App\Framework\Actions;

use App\Framework\Database\Hydrator;
use App\Framework\Database\NoRecordException;
use App\Framework\Database\Table;
use App\Framework\Session\FlashService;
use App\Framework\Validator;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CrudAction
 *
 * @package App\Framework\Actions
 */
class CrudAction
{

    /**
     *
     * @var RendererInterface
     */
    private $renderer;

    /**
     *
     * @var Router
     */
    private $router;

    /**
     *
     * @var Table
     */
    protected $table;

    /**
     *
     * @var FlashService
     */
    private $flash;

    /**
     *
     * @var string
     */
    protected $viewPath;

    /**
     *
     * @var string
     */
    protected $routePrefix;

    /**
     *
     * @var string
     */
    protected $messages = [
        'create' => "L'élément a bien été créé",
        'edit'   => "L'élément a bien été modifié",
    ];

    /**
     *
     * @var array
     */
    protected $acceptedParams = [];

    use RouterAwareAction;

    /**
     * CrudAction constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param Table $table
     * @param FlashService $flash
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $table,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->router   = $router;
        $this->table    = $table;
        $this->flash    = $flash;
    }//end __construct()

    /**
     * @param Request $request
     * @return ResponseInterface|string
     * @throws NoRecordException
     */
    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string) $request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }//end __invoke()

    /**
     * Affiche la liste des éléments
     *
     * @param  Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items  = $this->table->findAll()->paginate(12, ($params['p'] ?? 1));

        return $this->renderer->render($this->viewPath.'/index', compact('items'));
    }//end index()

    /**
     * Edite un élément
     *
     * @param  Request $request
     * @return ResponseInterface|string
     * @throws NoRecordException
     */
    public function edit(Request $request)
    {
        $id   = (int) $request->getAttribute('id');
        $item = $this->table->find($id);

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($id, $this->prePersist($request, $item));
                $this->postPersist($request, $item);
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix.'.index');
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item);
        }

        return $this->renderer->render(
            $this->viewPath.'/edit',
            $this->formParams(compact('item', 'errors'))
        );
    }//end edit()

    /**
     * Crée un nouvel élément
     *
     * @param  Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($this->prePersist($request, $item));
                $this->postPersist($request, $item);
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix.'.index');
            }
            Hydrator::hydrate($request->getParsedBody(), $item);
            $errors = $validator->getErrors();
        }

        return $this->renderer->render(
            $this->viewPath.'/create',
            $this->formParams(compact('item', 'errors'))
        );
    }//end create()

    /**
     * Action de suppression
     *
     * @param  Request $request
     * @return ResponseInterface
     */
    public function delete(Request $request)
    {
        $this->table->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix.'.index');
    }//end delete()

    /**
     * Filtre les paramètres reçu par la requête
     *
     * @param  Request $request
     * @return array
     */
    protected function prePersist(Request $request, $item): array
    {
        return array_filter(
            array_merge($request->getParsedBody(), $request->getUploadedFiles()),
            function ($key) {
                return \in_array($key, $this->acceptedParams, true);
            },
            ARRAY_FILTER_USE_KEY
        );
    }//end prePersist()

    /**
     * Permet d'effectuer un traitement après la persistence
     *
     * @param Request $request
     * @param $item
     */
    protected function postPersist(Request $request, $item): void
    {
    }//end postPersist()

    /**
     * Génère le validateur pour valider les données
     *
     * @param  Request $request
     * @return Validator
     */
    protected function getValidator(Request $request)
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }//end getValidator()

    /**
     * Génère une nouvelle entité pour l'action de création
     *
     * @return mixed
     */
    protected function getNewEntity()
    {
        $entity = $this->table->getEntity();
        return new $entity();
    }//end getNewEntity()

    /**
     * Permet de traiter les paramètres à envoyer à la vue
     *
     * @param  $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }//end formParams()
}//end class
