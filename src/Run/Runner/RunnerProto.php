<?php


namespace Run\Runner;


use Base\Render\RenderSetupComponent;
use Run\Component\Configuration;
use Verse\Run\Component\RunComponentProto;

abstract class RunnerProto 
{
    abstract public function run();
    
    /**
     * @return RunComponentProto[]
     */
    public function getDafaultComponents () 
    {
        return [
            new RenderSetupComponent(),
            new Configuration(),
        ];
    }
}