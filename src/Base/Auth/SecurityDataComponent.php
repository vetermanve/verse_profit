<?php


namespace Base\Auth;


use Base\Auth\CryptoProcessor\CryptoProcessorInterface;
use Base\Auth\CryptoProcessor\Sha256CryptoProcessor;
use Verse\Di\Env;
use Verse\Run\Component\RunComponentProto;

class SecurityDataComponent extends RunComponentProto
{
    const SECURITY_MODULE   = CryptoProcessorInterface::class;
    const CERT_PATH         = 'security_cert_path'; 
    const CERT_CURRENT_NAME = 'security_cert_name'; 
    
    public function run()
    {
        $certPath = $this->context->get(self::CERT_PATH);
        $currentCert = $this->context->get(self::CERT_CURRENT_NAME, 'main');
        
        $container = Env::getContainer();

        $container->setModule(CryptoProcessorInterface::class, function () use ($certPath, $currentCert) {
             if ($certPath && file_exists($certPath)) {
                 $certBody = file_get_contents($certPath);
             } else {
                 $certBody = 'DevCert';
             }
             
             $checker = new Sha256CryptoProcessor();
             $checker->setCertificate($certBody, $currentCert);
             
             return $checker;
        });
        
        $container->setModule(ChannelSecurityWrapperFactory::class, function () use ($container, $currentCert) {
            $channelStateWrapper = new ChannelSecurityWrapperFactory();
            $channelStateWrapper->setDefaultCert($currentCert);
            $channelStateWrapper->setContainer($container);
            
            return $channelStateWrapper;
        });
    }
}