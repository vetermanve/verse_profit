<?php


namespace Base\Auth\CryptoProcessor;


class Sha256CryptoProcessor implements CryptoProcessorInterface
{
    private $certificate = '';
    
    public function setCertificate(string $body, string $certificateName = 'main') : bool
    {
        if ('' === $body) {
            return false;
        }
        
        $this->certificate = $body;
        
        return true;
    }

    public function getSignature(string $data, string $certificateName = 'main') : string
    {
        return hash('sha256', $data.$this->certificate);    
    }

    public function checkSignature(string $data, string $signature, string $certificateName = 'main') : bool
    {
        return $this->getSignature($data, $certificateName) === $signature; 
    }
}