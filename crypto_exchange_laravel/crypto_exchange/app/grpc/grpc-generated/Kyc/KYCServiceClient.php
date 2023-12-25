<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Kyc;

/**
 * The KYC service definition.
 */
class KYCServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Sends a KYC request
     * @param \Kyc\KYCRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CheckKYC(\Kyc\KYCRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/kyc.KYCService/CheckKYC',
        $argument,
        ['\Kyc\KYCResponse', 'decode'],
        $metadata, $options);
    }

}
