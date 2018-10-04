<?php


namespace Base\Render\Twig;


use Base\Render\RendererInterface;

class TwigRenderer implements RendererInterface
{
    private $layoutDirectories = [];
    
    public function render(string $template, array $data = [], string $layout = 'main', array $templateDirectories = [])
    {
        $templateDirectories = array_merge($templateDirectories, $this->layoutDirectories);
        $fs = new \Twig_Loader_Filesystem($templateDirectories);
        $env = new \Twig_Environment($fs);
        
        $data['_layout'] = $layout.'.twig';
        return $env->render($template.'.twig', $data);
    }
    
    public function addLayoutDir ($dir) 
    {
        $this->layoutDirectories[] = $dir;
    }
}