$file = "c:\Users\LENOVO\OneDrive\Desktop\os-tiket last\os-tiket\app\Services\VpnDetectionService.php"
$content = [System.IO.File]::ReadAllText($file, [System.Text.Encoding]::UTF8)

$oldBlock = '        // ========== Decision Logic ==========
        $isMobile = $ispResult[''mobile''] ?? false;

        if ($cidrResult[''is_vpn'']) {
            $result[''is_vpn''] = true;
            $result[''confidence''] = 80;
            $result[''provider''] = $cidrResult[''provider''];
            $result[''details''][''cidr_match''] = $cidrResult[''provider''];
            $result[''details''][''decision''] = ''blocked_by_cidr'';
        } elseif ($isMobile) {
            if ($cidrResult[''is_vpn'']) {
                $result[''is_vpn''] = true;
                $result[''confidence''] = 80;
                $result[''provider''] = $cidrResult[''provider''];
                $result[''details''][''decision''] = ''blocked_by_cidr'';
                $result[''details''][''cidr_match''] = $cidrResult[''provider''];
            } elseif ($ispResult[''proxy''] ?? false) {
                $result[''is_vpn''] = true;
                $result[''confidence''] = 90;
                $result[''details''][''decision''] = ''blocked_by_proxy_flag'';
                if (!$result[''provider'']) {
                    $result[''provider''] = $ispResult[''isp_name''] ?: ''Proxy/VPN (ip-api)'';
                }
            } else {
                $result[''is_vpn''] = false;
                $result[''confidence''] = 0;
                $result[''details''][''decision''] = ''allowed_mobile_carrier'';
                $result[''details''][''mobile_carrier''] = $ispResult[''isp_name''] ?? ''Unknown'';
            }
        } else {
            if ($ispResult[''proxy''] ?? false) {
                $result[''is_vpn''] = true;
                $result[''confidence''] = 95;
                $result[''details''][''decision''] = ''blocked_by_proxy_flag'';
                if (!$result[''provider'']) {
                    $result[''provider''] = $ispResult[''isp_name''] ?: ''Proxy/VPN (ip-api)'';
                }
            } elseif ($ispResult[''hosting''] ?? false) {
                $result[''is_vpn''] = true;
                $result[''confidence''] = 85;
                $result[''details''][''decision''] = ''blocked_by_hosting_flag'';
                if (!$result[''provider'']) {
                    $result[''provider''] = $ispResult[''isp_name''] ?: ''Datacenter/Hosting (ip-api)'';
                }
            } else {
                $result[''details''][''decision''] = ''allowed_clean_ip'';
            }
        }'

$newBlock = '        // ========== Decision Logic ==========
        $country = $ispResult[''country''] ?? null;
        $isIndonesia = ($country === ''Indonesia'' || $country === ''ID'');

        if ($cidrResult[''is_vpn'']) {
            $result[''is_vpn''] = true;
            $result[''confidence''] = 80;
            $result[''provider''] = $cidrResult[''provider''];
            $result[''details''][''cidr_match''] = $cidrResult[''provider''];
            $result[''details''][''decision''] = ''blocked_by_cidr'';
        } elseif ($isIndonesia) {
            $result[''is_vpn''] = false;
            $result[''confidence''] = 0;
            $result[''details''][''decision''] = ''allowed_indonesia_ip'';
            $result[''details''][''reason''] = ''Indonesian IP - skipped ip-api flags (CGNAT)'';
        } else {
            if ($ispResult[''proxy''] ?? false) {
                $result[''is_vpn''] = true;
                $result[''confidence''] = 95;
                $result[''details''][''decision''] = ''blocked_by_proxy_flag'';
                if (!$result[''provider'']) {
                    $result[''provider''] = $ispResult[''isp_name''] ?: ''Proxy/VPN (ip-api)'';
                }
            } elseif ($ispResult[''hosting''] ?? false) {
                $result[''is_vpn''] = true;
                $result[''confidence''] = 85;
                $result[''details''][''decision''] = ''blocked_by_hosting_flag'';
                if (!$result[''provider'']) {
                    $result[''provider''] = $ispResult[''isp_name''] ?: ''Datacenter/Hosting (ip-api)'';
                }
            } else {
                $result[''details''][''decision''] = ''allowed_clean_ip'';
            }
        }'

if ($content.Contains($oldBlock)) {
    $content = $content.Replace($oldBlock, $newBlock)
    [System.IO.File]::WriteAllText($file, $content, [System.Text.Encoding]::UTF8)
    Write-Host "SUCCESS: Decision logic updated. Indonesia IPs will skip ip-api flags."
} else {
    Write-Host "FAILED: Old block not found in file."
    $pos = $content.IndexOf("Decision Logic")
    if ($pos -gt 0) {
        Write-Host "Found 'Decision Logic' at position $pos"
    }
}
