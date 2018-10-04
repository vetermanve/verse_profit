<?php

namespace Base\Render;

use Base\Render\Twig\TwigRenderer;
use Verse\Di\Env;
use Verse\Run\Component\RunComponentProto;

class RenderSetupComponent extends RunComponentProto
{

    public function run()
    {
        $layoutDir = __DIR__.'/Twig/Layouts';
        Env::getContainer()->setModule(RendererInterface::class, function () use ($layoutDir) {
            $renderer = new TwigRenderer();
            $renderer->addLayoutDir($layoutDir);
            return $renderer;
        });
    }
}