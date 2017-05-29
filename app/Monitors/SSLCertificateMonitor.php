<?php

namespace App\Monitors;

use Carbon\Carbon;
use Exception;

class SSLCertificateMonitor extends BaseMonitor
{
    /**  @var string */
    protected $status;

    /**
     * @param array $config
     */
    public function __construct()
    {
        $url = parse_url('https://www.nixler.pl');

        if (empty($url['scheme']) || $url['scheme'] != 'https') {
            throw new Exception("Nixler is not secure!", 1);
        }

        //DOWNLOAD CERTIFICATE
        $streamContext = stream_context_create([
            "ssl" => [
                "capture_peer_cert" => TRUE
            ]
        ]);

        $streamClient = stream_socket_client("ssl://{$url['host']}:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $streamContext);

        $certificateContext = stream_context_get_params($streamClient);

        $certificate = openssl_x509_parse($certificateContext['options']['ssl']['peer_certificate']);
        //END DOWNLOAD CERTIFICATE

        //PROCESS CERTIFICATE
        if (!empty($certificate['subject']) && !empty($certificate['subject']['CN'])) {
            $certDomain = $certificate['subject']['CN'];
        }

        if (!empty($certificate['validTo_time_t'])) {
            $validTo = Carbon::createFromTimestampUTC($certificate['validTo_time_t']);
            $due = - $validTo->diffInDays(Carbon::now(), false);
        }

        if (!empty($certificate['extensions']) && !empty($certificate['extensions']['subjectAltName'])) {
            $additionalDomains = [];
            $domains = explode(', ', $certificate['extensions']['subjectAltName']);
            foreach ($domains as $domain) {
                $additionalDomains[] = str_replace('DNS:', '', $domain);
            }
        }
        //END PROCESS CERTIFICATE

        if ($due < 0 || ! $this->isHostCovered($url['host'], $certDomain, $additionalDomains)) {
            $this->status = 'Invalid';
        } elseif ($due < 20) {
            $this->status = 'Expires in '.$due.' days';
        } else {
            $this->status = 'Valid';
        }

    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'SSL Certificate';
    }

    /**
     * @return boolean
     */
    public function hasErrors()
    {
        return ($this->status != 'Valid');
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->status;
    }

    public function isHostCovered($host, $certificateHost, array $certificateAdditionalDomains = [])
    {
        if ($host == $certificateHost) {
            return true;
        }

        // Determine if wildcard domain covers the host domain
        if ($certificateHost[0] == '*' && substr_count($host, '.') > 1) {
            $certificateHost = substr($certificateHost, 1);
            $host = substr($host, strpos($host, '.'));
            return $certificateHost == $host;
        }

        foreach ($certificateAdditionalDomains as $domain) {
            if ($domain[0] == '*' && substr_count($host, '.') > 1) {
                $domain = substr($domain, 1);
                $host = substr($host, strpos($host, '.'));
            }

            if($domain == $host) {
                return true;
            }
        }
    }

}