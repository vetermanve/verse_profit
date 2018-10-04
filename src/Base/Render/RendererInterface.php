<?php


namespace Base\Render;


interface RendererInterface
{
    public function render (string $template, array $data = [], string $layout = 'main', array $templateDirectories = []);
}