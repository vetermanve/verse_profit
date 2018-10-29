<?php


namespace Base\Auth\CryptoProcessor;


interface CryptoProcessorInterface
{
    public function setCertificate (string $body, string $certificateName = 'main') : bool;
    public function getSignature (string $data, string $certificateName = 'main') : string;
    public function checkSignature (string $data, string  $signature, string $certificateName = 'main') : bool;
}