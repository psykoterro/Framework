<?php
/**
 * Created by IntelliJ IDEA.
 * User: meg4r0m
 * Date: 10/06/18
 * Time: 00:55
 */

namespace App\Admin;

use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Class AdminTwigExtension
 * @package App\Admin
 */
class AdminTwigExtension extends Twig_Extension
{

    /**
     *
     * @var array
     */
    private $widgets;

    /**
     * AdminTwigExtension constructor.
     * @param array $widgets
     */
    public function __construct(array $widgets)
    {
        $this->widgets = $widgets;
    }//end __construct()

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [new Twig_SimpleFunction('admin_menu', [$this, 'renderMenu'], ['is_safe' => ['html']])];
    }//end getFunctions()

    /**
     * @return string
     */
    public function renderMenu(): string
    {
        return array_reduce(
            $this->widgets,
            function (string $html, AdminWidgetInterface $widget) {
                return $html.$widget->renderMenu();
            },
            ''
        );
    }//end renderMenu()
}//end class
