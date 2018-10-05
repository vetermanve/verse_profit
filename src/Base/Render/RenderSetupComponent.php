<?php

namespace Base\Render;

use Base\Render\Twig\TwigRenderer;
use Verse\Di\Env;
use Verse\Run\Component\RunComponentProto;

class RenderSetupComponent extends RunComponentProto
{

    public function run()
    {
        Env::getContainer()->setModule(RendererInterface::class, function (){
            return new TwigRenderer();
        });
    }
}