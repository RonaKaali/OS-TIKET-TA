<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VpnDetectionService
{
    /**
     * CIDR database for known VPN providers, datacenters, and hosting providers.
     * These ranges are typically associated with VPN/proxy/anonymizer traffic.
     *
     * Format: [ ['cidr' => '...', 'provider' => '...'], ... ]
     */
    protected array $knownVpnRanges = [];

    /**
     * ISP keywords that indicate VPN/proxy/datacenter hosting.
     */
    protected array $suspiciousIspKeywords = [
        'vpn', 'proxy', 'cloud', 'hosting', 'datacenter', 'server',
        'aws', 'amazon', 'google cloud', 'azure', 'microsoft',
        'digitalocean', 'digital ocean', 'vultr', 'linode', 'hetzner',
        'ovh', 'soyoustart', 'scaleway', 'online.net', 'kimsufi',
        'leaseweb', 'contabo', 'netcup', 'ionos', '1&1',
        'rackspace', 'softlayer', 'ibm cloud', 'oracle cloud',
        'alibaba cloud', 'upcloud', 'exoscale',
        'colo', 'dedicated', 'bare metal',
        'data center', 'data-centre',
        'webhosting', 'web host',
        'vpn service', 'anonymous', 'anonymizer',
        'nordvpn', 'expressvpn', 'protonvpn', 'proton',
        'cyberghost', 'windscribe', 'private internet access',
        'ipvanish', 'hotspot shield', 'surfshark',
        'tor exit', 'tor node', 'the tor project',
        'public proxy', 'socks', 'elite proxy',
        'vps', 'vds', 'virtual private server',
        'fly.io', 'vercel', 'netlify', 'heroku',
        'layer7', 'ddos-guard',
    ];

    /**
     * Indonesian ISP names that are SAFE (not VPN/proxy).
     * We exclude these from keyword matching to avoid false positives.
     */
    protected array $safeIndonesianIsps = [
        'telkom', 'telkomsel', 'indihome', 'first media', 'm3 connect',
        'xl axiata', 'xl', 'tri', '3 indonesia', 'smartfren',
        'indosat', 'im3', 'my republic', 'mora', 'moratelindo',
        'biznet', 'biznet networks', 'iconnet', 'icon+', 'pln icon+',
        'centrin', 'cbn', 'cyberindo', 'd Networks', 'danpac',
        'global media', 'jalin', 'lintasarta', 'nex hub',
        'nusanet', 'nusantara', 'orange', 'pacific net',
        'prima', 'radnet', 'supra', 'varnion', 'wifi.id',
        'by.u', 'byu', 'big data', 'indonesia',
    ];

    public function __construct()
    {
        $this->knownVpnRanges = $this->buildVpnRanges();
    }

    /**
     * Build the VPN CIDR ranges as an indexed array to avoid duplicate key issues.
     */
    protected function buildVpnRanges(): array
    {
        return [
            // === NordVPN ===
            ['cidr' => '185.225.232.0/22', 'provider' => 'NordVPN'],
            ['cidr' => '212.17.0.0/16', 'provider' => 'NordVPN'],
            ['cidr' => '45.132.0.0/16', 'provider' => 'NordVPN'],
            ['cidr' => '185.226.132.0/22', 'provider' => 'NordVPN'],
            ['cidr' => '185.225.236.0/22', 'provider' => 'NordVPN'],
            ['cidr' => '185.226.140.0/22', 'provider' => 'NordVPN'],
            ['cidr' => '45.133.216.0/22', 'provider' => 'NordVPN'],
            ['cidr' => '91.239.108.0/22', 'provider' => 'NordVPN'],

            // === ExpressVPN ===
            ['cidr' => '104.129.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '104.250.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '108.62.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '23.195.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '23.226.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '23.237.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '23.246.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '23.247.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '63.217.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '64.77.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '66.115.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '69.164.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '69.166.0.0/16', 'provider' => 'ExpressVPN'],
            ['cidr' => '77.245.112.0/20', 'provider' => 'ExpressVPN'],
            ['cidr' => '96.47.224.0/20', 'provider' => 'ExpressVPN'],

            // === ProtonVPN ===
            ['cidr' => '185.159.156.0/22', 'provider' => 'ProtonVPN'],
            ['cidr' => '185.159.157.0/24', 'provider' => 'ProtonVPN'],
            ['cidr' => '185.159.158.0/24', 'provider' => 'ProtonVPN'],
            ['cidr' => '185.228.120.0/22', 'provider' => 'ProtonVPN'],
            ['cidr' => '212.91.74.0/24', 'provider' => 'ProtonVPN'],
            ['cidr' => '212.91.75.0/24', 'provider' => 'ProtonVPN'],
            ['cidr' => '95.216.0.0/16', 'provider' => 'ProtonVPN'],
            ['cidr' => '188.40.0.0/16', 'provider' => 'ProtonVPN'],

            // === Cloudflare WARP / 1.1.1.1 VPN ===
            ['cidr' => '162.159.192.0/18', 'provider' => 'Cloudflare WARP'],
            ['cidr' => '162.159.200.0/21', 'provider' => 'Cloudflare WARP'],
            ['cidr' => '162.159.208.0/20', 'provider' => 'Cloudflare WARP'],
            ['cidr' => '162.159.224.0/19', 'provider' => 'Cloudflare WARP'],
            ['cidr' => '162.159.248.0/21', 'provider' => 'Cloudflare WARP'],
            ['cidr' => '188.114.96.0/20', 'provider' => 'Cloudflare WARP'],
            ['cidr' => '188.114.100.0/22', 'provider' => 'Cloudflare WARP'],

            // === CyberGhost VPN ===
            ['cidr' => '185.180.12.0/22', 'provider' => 'CyberGhost VPN'],
            ['cidr' => '212.56.200.0/21', 'provider' => 'CyberGhost VPN'],
            ['cidr' => '5.253.60.0/22', 'provider' => 'CyberGhost VPN'],
            ['cidr' => '89.36.216.0/21', 'provider' => 'CyberGhost VPN'],
            ['cidr' => '89.36.224.0/21', 'provider' => 'CyberGhost VPN'],

            // === Hotspot Shield / AnchorFree ===
            ['cidr' => '72.14.176.0/20', 'provider' => 'Hotspot Shield'],
            ['cidr' => '72.14.180.0/22', 'provider' => 'Hotspot Shield'],
            ['cidr' => '72.14.184.0/21', 'provider' => 'Hotspot Shield'],
            ['cidr' => '209.197.0.0/16', 'provider' => 'Hotspot Shield'],

            // === Private Internet Access (PIA) ===
            ['cidr' => '23.94.0.0/15', 'provider' => 'Private Internet Access'],
            ['cidr' => '64.79.96.0/20', 'provider' => 'Private Internet Access'],
            ['cidr' => '66.115.128.0/19', 'provider' => 'Private Internet Access'],
            ['cidr' => '69.30.192.0/18', 'provider' => 'Private Internet Access'],
            ['cidr' => '104.128.0.0/14', 'provider' => 'Private Internet Access'],

            // === Windscribe VPN ===
            ['cidr' => '107.155.0.0/17', 'provider' => 'Windscribe VPN'],
            ['cidr' => '137.220.0.0/16', 'provider' => 'Windscribe VPN'],
            ['cidr' => '185.156.44.0/22', 'provider' => 'Windscribe VPN'],
            ['cidr' => '192.157.192.0/18', 'provider' => 'Windscribe VPN'],

            // === Surfshark ===
            ['cidr' => '45.128.132.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '45.129.96.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '45.129.100.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '45.129.104.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '45.129.108.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '45.132.16.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '45.132.20.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '45.132.24.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '46.245.152.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '91.149.232.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '95.128.36.0/22', 'provider' => 'Surfshark'],
            ['cidr' => '95.128.40.0/22', 'provider' => 'Surfshark'],

            // === VyprVPN ===
            ['cidr' => '208.69.32.0/19', 'provider' => 'VyprVPN'],
            ['cidr' => '66.235.176.0/20', 'provider' => 'VyprVPN'],

            // === TunnelBear ===
            ['cidr' => '141.193.249.0/24', 'provider' => 'TunnelBear'],
            ['cidr' => '141.193.250.0/24', 'provider' => 'TunnelBear'],
            ['cidr' => '141.193.251.0/24', 'provider' => 'TunnelBear'],

            // === IPVanish ===
            ['cidr' => '23.239.8.0/21', 'provider' => 'IPVanish'],
            ['cidr' => '66.115.128.0/17', 'provider' => 'IPVanish'],
            ['cidr' => '67.227.176.0/20', 'provider' => 'IPVanish'],
            ['cidr' => '69.30.192.0/17', 'provider' => 'IPVanish'],
            ['cidr' => '104.167.0.0/17', 'provider' => 'IPVanish'],
            ['cidr' => '162.244.0.0/17', 'provider' => 'IPVanish'],
            ['cidr' => '172.82.128.0/17', 'provider' => 'IPVanish'],
            ['cidr' => '192.157.192.0/17', 'provider' => 'IPVanish'],

            // === Tor Exit Nodes ===
            ['cidr' => '128.31.0.0/16', 'provider' => 'Tor Exit Node'],
            ['cidr' => '131.188.0.0/16', 'provider' => 'Tor Exit Node'],
            ['cidr' => '154.35.132.0/24', 'provider' => 'Tor Exit Node'],
            ['cidr' => '185.100.84.0/22', 'provider' => 'Tor Exit Node'],
            ['cidr' => '185.165.168.0/24', 'provider' => 'Tor Exit Node'],
            ['cidr' => '185.220.100.0/22', 'provider' => 'Tor Exit Node'],
            ['cidr' => '185.220.101.0/24', 'provider' => 'Tor Exit Node'],
            ['cidr' => '193.218.118.0/24', 'provider' => 'Tor Exit Node'],
            ['cidr' => '199.249.224.0/22', 'provider' => 'Tor Exit Node'],
            ['cidr' => '23.129.64.0/24', 'provider' => 'Tor Exit Node'],
            ['cidr' => '37.218.240.0/20', 'provider' => 'Tor Exit Node'],

            // === Amazon Web Services (AWS) ===
            ['cidr' => '13.32.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.34.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.48.0.0/17', 'provider' => 'AWS'],
            ['cidr' => '13.48.128.0/18', 'provider' => 'AWS'],
            ['cidr' => '13.52.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '13.54.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.56.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '13.58.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.124.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '13.126.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.208.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '13.209.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '13.210.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.212.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.214.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.228.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.230.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '13.232.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '13.236.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '13.248.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '13.250.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '15.152.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '15.164.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '15.177.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '15.184.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '15.185.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '15.188.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '15.193.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '15.220.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '15.228.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '15.230.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '15.236.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.130.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.132.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '18.136.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.138.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.140.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.141.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.153.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.155.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.157.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.158.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.162.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.175.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.177.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.178.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.179.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.180.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.182.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.183.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.184.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.188.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.191.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.192.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.194.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.196.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.198.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.200.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.201.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '18.220.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '18.221.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '3.0.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '3.4.12.0/22', 'provider' => 'AWS'],
            ['cidr' => '3.5.0.0/19', 'provider' => 'AWS'],
            ['cidr' => '3.5.128.0/22', 'provider' => 'AWS'],
            ['cidr' => '3.5.132.0/23', 'provider' => 'AWS'],
            ['cidr' => '3.5.134.0/24', 'provider' => 'AWS'],
            ['cidr' => '3.5.48.0/22', 'provider' => 'AWS'],
            ['cidr' => '3.5.52.0/23', 'provider' => 'AWS'],
            ['cidr' => '3.5.54.0/24', 'provider' => 'AWS'],
            ['cidr' => '3.5.252.0/22', 'provider' => 'AWS'],
            ['cidr' => '3.5.248.0/22', 'provider' => 'AWS'],
            ['cidr' => '3.8.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.16.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.80.0.0/12', 'provider' => 'AWS'],
            ['cidr' => '3.96.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '3.98.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '3.100.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '3.101.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '3.104.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.108.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.112.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.120.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.124.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.128.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '3.130.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '3.131.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '3.132.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.136.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '3.144.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '3.152.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '3.160.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.164.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.168.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.172.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '3.208.0.0/12', 'provider' => 'AWS'],
            ['cidr' => '3.224.0.0/12', 'provider' => 'AWS'],
            ['cidr' => '3.248.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '3.252.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '3.253.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '3.254.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '3.255.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '34.192.0.0/10', 'provider' => 'AWS'],
            ['cidr' => '35.152.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '35.160.0.0/12', 'provider' => 'AWS'],
            ['cidr' => '35.176.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '44.192.0.0/10', 'provider' => 'AWS'],
            ['cidr' => '46.51.192.0/20', 'provider' => 'AWS'],
            ['cidr' => '46.51.208.0/21', 'provider' => 'AWS'],
            ['cidr' => '46.51.216.0/21', 'provider' => 'AWS'],
            ['cidr' => '46.137.0.0/17', 'provider' => 'AWS'],
            ['cidr' => '50.16.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '50.18.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '50.19.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.0.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.2.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.4.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.8.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.10.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.12.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.15.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.16.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '52.24.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.28.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.30.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.32.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.36.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.40.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.44.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.46.0.0/18', 'provider' => 'AWS'],
            ['cidr' => '52.47.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.48.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.52.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.54.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.56.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.57.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.58.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.60.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.64.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.68.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.72.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.74.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.76.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.77.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.78.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.79.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.80.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.84.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.86.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.88.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.92.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.94.12.0/24', 'provider' => 'AWS'],
            ['cidr' => '52.94.13.0/24', 'provider' => 'AWS'],
            ['cidr' => '52.94.76.0/22', 'provider' => 'AWS'],
            ['cidr' => '52.94.124.0/22', 'provider' => 'AWS'],
            ['cidr' => '52.95.52.0/22', 'provider' => 'AWS'],
            ['cidr' => '52.95.108.0/23', 'provider' => 'AWS'],
            ['cidr' => '52.95.110.0/24', 'provider' => 'AWS'],
            ['cidr' => '52.95.146.0/23', 'provider' => 'AWS'],
            ['cidr' => '52.95.148.0/23', 'provider' => 'AWS'],
            ['cidr' => '52.95.150.0/24', 'provider' => 'AWS'],
            ['cidr' => '52.95.220.0/23', 'provider' => 'AWS'],
            ['cidr' => '52.95.248.0/22', 'provider' => 'AWS'],
            ['cidr' => '52.95.252.0/24', 'provider' => 'AWS'],
            ['cidr' => '52.95.255.0/24', 'provider' => 'AWS'],
            ['cidr' => '52.96.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.119.192.0/20', 'provider' => 'AWS'],
            ['cidr' => '52.119.208.0/24', 'provider' => 'AWS'],
            ['cidr' => '52.144.0.0/12', 'provider' => 'AWS'],
            ['cidr' => '52.192.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '52.200.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.204.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.208.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '52.216.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.218.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.220.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.221.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '52.222.0.0/17', 'provider' => 'AWS'],
            ['cidr' => '52.223.0.0/17', 'provider' => 'AWS'],
            ['cidr' => '52.223.128.0/18', 'provider' => 'AWS'],
            ['cidr' => '52.240.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.242.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.244.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.246.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.248.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '52.252.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.254.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '52.255.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.36.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.38.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.39.0.0/17', 'provider' => 'AWS'],
            ['cidr' => '54.46.0.0/17', 'provider' => 'AWS'],
            ['cidr' => '54.67.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.68.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '54.72.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '54.77.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.78.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.80.0.0/12', 'provider' => 'AWS'],
            ['cidr' => '54.144.0.0/12', 'provider' => 'AWS'],
            ['cidr' => '54.160.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '54.168.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.169.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.170.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.172.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.174.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.176.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '54.184.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '54.192.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '54.208.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '54.216.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '54.220.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.222.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.224.0.0/13', 'provider' => 'AWS'],
            ['cidr' => '54.232.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '54.236.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.238.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.240.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.242.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.244.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.245.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.246.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.247.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.248.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '54.250.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.251.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.252.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.253.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.254.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '54.255.0.0/16', 'provider' => 'AWS'],
            ['cidr' => '56.136.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '63.32.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '63.176.0.0/14', 'provider' => 'AWS'],
            ['cidr' => '64.252.64.0/18', 'provider' => 'AWS'],
            ['cidr' => '75.2.0.0/15', 'provider' => 'AWS'],
            ['cidr' => '76.223.0.0/17', 'provider' => 'AWS'],
            ['cidr' => '96.0.0.0/16', 'provider' => 'AWS'],

            // === Google Cloud Platform (GCP) ===
            ['cidr' => '8.34.208.0/20', 'provider' => 'Google Cloud'],
            ['cidr' => '8.35.192.0/21', 'provider' => 'Google Cloud'],
            ['cidr' => '8.35.200.0/23', 'provider' => 'Google Cloud'],
            ['cidr' => '8.35.202.0/24', 'provider' => 'Google Cloud'],
            ['cidr' => '8.35.203.0/24', 'provider' => 'Google Cloud'],
            ['cidr' => '23.236.48.0/20', 'provider' => 'Google Cloud'],
            ['cidr' => '23.251.128.0/19', 'provider' => 'Google Cloud'],
            ['cidr' => '34.0.0.0/15', 'provider' => 'Google Cloud'],
            ['cidr' => '34.2.0.0/15', 'provider' => 'Google Cloud'],
            ['cidr' => '34.4.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '34.8.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '34.16.0.0/12', 'provider' => 'Google Cloud'],
            ['cidr' => '34.32.0.0/11', 'provider' => 'Google Cloud'],
            ['cidr' => '34.64.0.0/11', 'provider' => 'Google Cloud'],
            ['cidr' => '34.96.0.0/12', 'provider' => 'Google Cloud'],
            ['cidr' => '34.112.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '34.124.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '34.128.0.0/15', 'provider' => 'Google Cloud'],
            ['cidr' => '34.128.128.0/18', 'provider' => 'Google Cloud'],
            ['cidr' => '34.128.192.0/20', 'provider' => 'Google Cloud'],
            ['cidr' => '34.132.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '34.136.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '34.144.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '34.152.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '34.156.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '34.160.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '34.168.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '35.184.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '35.192.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '35.196.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '35.200.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '35.208.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '35.216.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '35.220.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '35.224.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '35.232.0.0/13', 'provider' => 'Google Cloud'],
            ['cidr' => '35.240.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '35.244.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '35.248.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '35.252.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '104.154.0.0/15', 'provider' => 'Google Cloud'],
            ['cidr' => '104.196.0.0/14', 'provider' => 'Google Cloud'],
            ['cidr' => '104.198.0.0/15', 'provider' => 'Google Cloud'],
            ['cidr' => '104.200.0.0/15', 'provider' => 'Google Cloud'],
            ['cidr' => '107.167.160.0/19', 'provider' => 'Google Cloud'],
            ['cidr' => '107.178.192.0/18', 'provider' => 'Google Cloud'],
            ['cidr' => '108.59.80.0/20', 'provider' => 'Google Cloud'],
            ['cidr' => '130.211.0.0/16', 'provider' => 'Google Cloud'],
            ['cidr' => '146.148.0.0/17', 'provider' => 'Google Cloud'],
            ['cidr' => '162.222.176.0/21', 'provider' => 'Google Cloud'],
            ['cidr' => '172.217.0.0/16', 'provider' => 'Google Cloud'],
            ['cidr' => '172.253.0.0/16', 'provider' => 'Google Cloud'],
            ['cidr' => '173.194.0.0/16', 'provider' => 'Google Cloud'],
            ['cidr' => '173.255.112.0/20', 'provider' => 'Google Cloud'],
            ['cidr' => '192.158.28.0/22', 'provider' => 'Google Cloud'],
            ['cidr' => '199.192.112.0/22', 'provider' => 'Google Cloud'],
            ['cidr' => '199.223.232.0/22', 'provider' => 'Google Cloud'],
            ['cidr' => '207.223.160.0/20', 'provider' => 'Google Cloud'],
            ['cidr' => '209.85.128.0/17', 'provider' => 'Google Cloud'],
            ['cidr' => '216.58.192.0/19', 'provider' => 'Google Cloud'],
            ['cidr' => '216.239.32.0/19', 'provider' => 'Google Cloud'],

            // === Microsoft Azure ===
            ['cidr' => '4.0.0.0/8', 'provider' => 'Microsoft Azure'],
            ['cidr' => '13.64.0.0/11', 'provider' => 'Microsoft Azure'],
            ['cidr' => '13.96.0.0/13', 'provider' => 'Microsoft Azure'],
            ['cidr' => '13.104.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '13.112.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '13.120.0.0/16', 'provider' => 'Microsoft Azure'],
            ['cidr' => '20.0.0.0/8', 'provider' => 'Microsoft Azure'],
            ['cidr' => '23.96.0.0/13', 'provider' => 'Microsoft Azure'],
            ['cidr' => '40.64.0.0/10', 'provider' => 'Microsoft Azure'],
            ['cidr' => '40.112.0.0/13', 'provider' => 'Microsoft Azure'],
            ['cidr' => '40.120.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '40.124.0.0/16', 'provider' => 'Microsoft Azure'],
            ['cidr' => '40.126.0.0/18', 'provider' => 'Microsoft Azure'],
            ['cidr' => '51.0.0.0/8', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.128.0.0/15', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.130.0.0/15', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.132.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.136.0.0/13', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.145.0.0/16', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.146.0.0/15', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.148.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.152.0.0/15', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.154.0.0/15', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.156.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.160.0.0/11', 'provider' => 'Microsoft Azure'],
            ['cidr' => '52.224.0.0/11', 'provider' => 'Microsoft Azure'],
            ['cidr' => '65.52.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '70.37.0.0/16', 'provider' => 'Microsoft Azure'],
            ['cidr' => '104.208.0.0/13', 'provider' => 'Microsoft Azure'],
            ['cidr' => '104.210.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '104.214.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '104.218.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '137.116.0.0/15', 'provider' => 'Microsoft Azure'],
            ['cidr' => '137.135.0.0/16', 'provider' => 'Microsoft Azure'],
            ['cidr' => '138.91.0.0/16', 'provider' => 'Microsoft Azure'],
            ['cidr' => '157.55.0.0/16', 'provider' => 'Microsoft Azure'],
            ['cidr' => '157.56.0.0/15', 'provider' => 'Microsoft Azure'],
            ['cidr' => '157.58.0.0/16', 'provider' => 'Microsoft Azure'],
            ['cidr' => '157.59.0.0/16', 'provider' => 'Microsoft Azure'],
            ['cidr' => '168.62.0.0/15', 'provider' => 'Microsoft Azure'],
            ['cidr' => '191.232.0.0/13', 'provider' => 'Microsoft Azure'],
            ['cidr' => '191.234.0.0/15', 'provider' => 'Microsoft Azure'],
            ['cidr' => '191.236.0.0/14', 'provider' => 'Microsoft Azure'],
            ['cidr' => '191.239.0.0/16', 'provider' => 'Microsoft Azure'],

            // === DigitalOcean ===
            ['cidr' => '104.131.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '104.236.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '107.170.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '128.199.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '137.184.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '138.197.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '138.68.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '139.59.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '142.93.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '143.110.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '144.126.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '146.190.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '147.182.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '157.230.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '157.245.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '159.65.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '159.89.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '159.203.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '161.35.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '162.243.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '164.90.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '165.22.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '165.227.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '167.71.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '167.99.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '167.172.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '168.119.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '170.64.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '174.138.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '178.62.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '178.128.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '185.34.172.0/22', 'provider' => 'DigitalOcean'],
            ['cidr' => '188.166.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '192.241.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '198.199.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '206.189.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '207.154.0.0/16', 'provider' => 'DigitalOcean'],
            ['cidr' => '209.97.0.0/16', 'provider' => 'DigitalOcean'],

            // === Vultr ===
            ['cidr' => '104.238.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '107.173.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '107.175.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '108.61.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '123.123.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '136.233.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '141.164.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '144.202.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '145.239.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '149.28.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '155.138.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '158.247.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '192.248.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '207.246.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '216.238.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '23.89.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '23.92.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '23.95.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '37.139.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '45.32.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '45.63.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '45.76.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '45.77.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '66.42.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '67.205.128.0/17', 'provider' => 'Vultr'],
            ['cidr' => '95.179.0.0/16', 'provider' => 'Vultr'],
            ['cidr' => '95.216.0.0/16', 'provider' => 'Vultr'],

            // === OVH ===
            ['cidr' => '37.59.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '46.105.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.38.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.68.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.75.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.77.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.79.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.81.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.83.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.89.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.91.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.159.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.178.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.195.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.210.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '51.222.0.0/15', 'provider' => 'OVH'],
            ['cidr' => '54.36.0.0/15', 'provider' => 'OVH'],
            ['cidr' => '54.37.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '57.128.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '91.121.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '92.222.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '94.23.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '144.217.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '145.239.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '147.135.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '149.56.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '151.80.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '158.69.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '162.19.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '167.114.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '176.31.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '178.32.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '178.33.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '188.165.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '193.70.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '198.27.0.0/16', 'provider' => 'OVH'],
            ['cidr' => '213.186.33.0/19', 'provider' => 'OVH'],

            // === Hetzner ===
            ['cidr' => '5.9.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '23.88.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '46.4.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '49.12.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '49.13.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '65.21.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '78.46.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '85.10.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '88.198.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '91.190.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '94.130.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '95.216.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '116.202.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '136.243.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '142.132.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '144.76.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '148.251.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '157.90.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '159.69.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '162.55.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '167.235.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '168.119.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '171.67.70.0/24', 'provider' => 'Hetzner'],
            ['cidr' => '176.9.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '178.63.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '188.40.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '193.29.51.0/24', 'provider' => 'Hetzner'],
            ['cidr' => '193.29.52.0/24', 'provider' => 'Hetzner'],
            ['cidr' => '193.29.53.0/24', 'provider' => 'Hetzner'],
            ['cidr' => '194.52.212.0/24', 'provider' => 'Hetzner'],
            ['cidr' => '194.52.213.0/24', 'provider' => 'Hetzner'],
            ['cidr' => '194.52.214.0/24', 'provider' => 'Hetzner'],
            ['cidr' => '194.52.215.0/24', 'provider' => 'Hetzner'],
            ['cidr' => '195.201.0.0/16', 'provider' => 'Hetzner'],
            ['cidr' => '213.239.0.0/16', 'provider' => 'Hetzner'],

            // === Linode ===
            ['cidr' => '23.92.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '45.33.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '45.56.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '45.79.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '50.116.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '66.175.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '69.164.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '72.14.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '74.207.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '96.126.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '97.107.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '104.200.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '106.187.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '139.162.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '172.104.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '173.230.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '173.255.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '176.58.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '178.79.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '184.106.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '192.155.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '192.237.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '192.81.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '198.58.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '199.168.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '207.192.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '209.237.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '212.111.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '216.194.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '218.189.0.0/16', 'provider' => 'Linode'],
            ['cidr' => '2600:3c00::/24', 'provider' => 'Linode'],
        ];
    }
    /**
     * Check if an IP address is associated with a VPN/proxy/datacenter.
     *
     * CRITICAL: Mobile IPs (4G/5G) are NEVER blocked based on ip-api flags alone.
     * Only definitive CIDR matches can block mobile IPs.
     * This prevents false positives from CGNAT/operator-grade NAT.
     */
    public function isVpn(string $ip): array
    {
        // Skip local/private IPs
        if ($this->isPrivateIp($ip)) {
            return [
                'is_vpn' => false,
                'confidence' => 0,
                'provider' => null,
                'details' => ['note' => 'Private/local IP, skipped'],
            ];
        }

        $result = [
            'is_vpn' => false,
            'confidence' => 0,
            'provider' => null,
            'details' => [],
        ];

        // ========== Layer 1: CIDR lookup (definitive) ==========
        $cidrResult = $this->checkCidrRanges($ip);

        // ========== Layer 2: ip-api.com lookup ==========
        $ispResult = $this->lookupIsp($ip);

        // If ISP lookup succeeded, populate details
        if (isset($ispResult['isp_name']) && $ispResult['isp_name']) {
            $result['details'] = [
                'isp' => $ispResult['isp_name'],
                'org' => $ispResult['org'] ?? null,
                'country' => $ispResult['country'] ?? null,
                'proxy_flag' => $ispResult['proxy'] ?? false,
                'hosting_flag' => $ispResult['hosting'] ?? false,
                'mobile_flag' => $ispResult['mobile'] ?? false,
            ];
        }

        // ========== Decision Logic ==========
        $isMobile = $ispResult['mobile'] ?? false;

        if ($cidrResult['is_vpn']) {
            // CIDR match is definitive - block regardless
            $result['is_vpn'] = true;
            $result['confidence'] = 80;
            $result['provider'] = $cidrResult['provider'];
            $result['details']['cidr_match'] = $cidrResult['provider'];
            $result['details']['decision'] = 'blocked_by_cidr';
        } elseif ($isMobile) {
            // Mobile IPs are NEVER blocked by ip-api flags (CGNAT false positives)
            $result['is_vpn'] = false;
            $result['confidence'] = 0;
            $result['details']['decision'] = 'allowed_mobile_carrier';
            $result['details']['mobile_carrier'] = $ispResult['isp_name'] ?? 'Unknown';
        } else {
            // Non-mobile IPs: check ip-api flags
            if ($ispResult['proxy'] ?? false) {
                $result['is_vpn'] = true;
                $result['confidence'] = 95;
                $result['details']['decision'] = 'blocked_by_proxy_flag';
                if (!$result['provider']) {
                    $result['provider'] = $ispResult['isp_name'] ?: 'Proxy/VPN (ip-api)';
                }
            } elseif ($ispResult['hosting'] ?? false) {
                $result['is_vpn'] = true;
                $result['confidence'] = 85;
                $result['details']['decision'] = 'blocked_by_hosting_flag';
                if (!$result['provider']) {
                    $result['provider'] = $ispResult['isp_name'] ?: 'Datacenter/Hosting (ip-api)';
                }
            } elseif ($ispResult['is_suspicious'] ?? false) {
                $result['is_vpn'] = true;
                $result['confidence'] = 70;
                $result['details']['decision'] = 'blocked_by_keyword';
                if (!$result['provider']) {
                    $result['provider'] = $ispResult['isp_name'];
                }
            } else {
                $result['details']['decision'] = 'allowed_clean_ip';
            }
        }

        return $result;
    }

    /**
     * Check IP against known CIDR ranges.
     */
    protected function checkCidrRanges(string $ip): array
    {
        foreach ($this->knownVpnRanges as $entry) {
            if ($this->ipInCidr($ip, $entry['cidr'])) {
                return [
                    'is_vpn' => true,
                    'provider' => $entry['provider'],
                ];
            }
        }

        return ['is_vpn' => false, 'provider' => null];
    }

    /**
     * Check if an IP is within a CIDR range.
     */
    protected function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr);
        $bits = (int) $bits;

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $ipLong = (float) sprintf('%u', $ipLong);
        $subnetLong = (float) sprintf('%u', $subnetLong);

        if ($bits === 0) {
            return true;
        }

        $mask = -1 << (32 - $bits);
        $mask = (float) sprintf('%u', $mask);

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    /**
     * Lookup ISP + proxy/hosting information for an IP address using ip-api.com.
     *
     * ip-api.com is free for up to 45 requests/minute from a single IP.
     * It provides direct 'proxy', 'hosting', and 'mobile' flags that are
     * highly reliable for VPN/proxy/datacenter detection.
     *
     * Results are cached for 24 hours to respect rate limits.
     */
    protected function lookupIsp(string $ip): array
    {
        $cacheKey = "vpn_detection:isp:{$ip}";
        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        // Skip for local/private IPs
        if ($this->isPrivateIp($ip)) {
            $result = [
                'isp_name' => null,
                'org' => null,
                'country' => null,
                'proxy' => false,
                'hosting' => false,
                'mobile' => false,
                'is_suspicious' => false,
            ];
            Cache::put($cacheKey, $result, now()->addHours(24));
            return $result;
        }

        try {
            // Fields: proxy=is proxy/vpn, hosting=is hosting/datacenter, mobile=is mobile IP
            $response = Http::timeout(5)
                ->retry(2, 1000)
                ->get("http://ip-api.com/json/{$ip}?fields=status,proxy,hosting,mobile,isp,org,country,countryCode,query");

            if ($response->successful()) {
                $data = $response->json();

                if (($data['status'] ?? '') === 'success') {
                    $proxy = (bool) ($data['proxy'] ?? false);
                    $hosting = (bool) ($data['hosting'] ?? false);
                    $mobile = (bool) ($data['mobile'] ?? false);
                    $isp = $data['isp'] ?? '';
                    $org = $data['org'] ?? '';
                    $country = $data['country'] ?? null;

                    $isSuspicious = $this->isSuspiciousIsp($isp) || $this->isSuspiciousIsp($org);

                    $result = [
                        'isp_name' => $isp ?: null,
                        'org' => $org ?: null,
                        'country' => $country,
                        'proxy' => $proxy,
                        'hosting' => $hosting,
                        'mobile' => $mobile,
                        'is_suspicious' => $isSuspicious,
                    ];

                    // Log deteksi untuk debugging (hanya jika proxy/hosting terdeteksi)
                    if ($proxy || $hosting) {
                        Log::info('VPN Detection: IP flagged', [
                            'ip' => $ip,
                            'proxy' => $proxy,
                            'hosting' => $hosting,
                            'mobile' => $mobile,
                            'isp' => $isp,
                            'org' => $org,
                            'country' => $country,
                        ]);
                    }

                    Cache::put($cacheKey, $result, now()->addHours(24));

                    return $result;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('VPN Detection: ISP lookup failed for IP ' . $ip . ': ' . $e->getMessage());
        }

        $default = [
            'isp_name' => null,
            'org' => null,
            'country' => null,
            'proxy' => false,
            'hosting' => false,
            'mobile' => false,
            'is_suspicious' => false,
        ];
        Cache::put($cacheKey, $default, now()->addMinutes(5));
        return $default;
    }

    /**
     * Check if an ISP name contains suspicious keywords.
     */
    protected function isSuspiciousIsp(string $isp): bool
    {
        $ispLower = strtolower($isp);

        // Skip jika ISP Indonesia yang dikenal aman
        foreach ($this->safeIndonesianIsps as $safe) {
            if (str_contains($ispLower, $safe)) {
                return false;
            }
        }

        foreach ($this->suspiciousIspKeywords as $keyword) {
            if (str_contains($ispLower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an IP is a private/local address.
     */
    protected function isPrivateIp(string $ip): bool
    {
        if ($ip === '::1') {
            return true;
        }

        $long = ip2long($ip);
        if ($long === false) {
            return true;
        }

        // 127.0.0.0/8
        if (($long & 0xFF000000) === 0x7F000000) {
            return true;
        }

        // 10.0.0.0/8
        if (($long & 0xFF000000) === 0x0A000000) {
            return true;
        }

        // 172.16.0.0/12
        if (($long & 0xFFF00000) === 0xAC100000) {
            return true;
        }

        // 192.168.0.0/16
        if (($long & 0xFFFF0000) === 0xC0A80000) {
            return true;
        }

        return false;
    }
}
