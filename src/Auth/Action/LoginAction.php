<?php
/**
 * Created by PhpStorm.
 * User: fdurano
 * Date: 19/06/18
 * Time: 23:28
 */

namespace App\Auth\Action;

use Framework\Renderer\RendererInterface;

/**
 * Class LoginAction
 * @package App\Auth\Action
 */
class LoginAction
{

    /**
     *
     * @var RendererInterface
     */
    private $renderer;

    /**
     * LoginAction constructor.
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }//end __construct()

    /**
     * @return string
     */
    public function __invoke()
    {
        return $this->renderer->render('@auth/login');
    }//end __invoke()
}//end class
