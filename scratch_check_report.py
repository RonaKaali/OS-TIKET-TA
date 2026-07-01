import docx
doc = docx.Document(r'c:\Users\LENOVO\Downloads\Laporan_TA_Final_LB.docx')

keywords = ['MFA', 'backup code', 'TOTP', 'Google2FA', 
            'Spatie', 'RBAC', 'least privilege',
            'device fingerprint', 'context-aware', 'context aware',
            'risk score', 'anomaly', 'impossible travel',
            'session verification', 'continuous session',
            'security event', 'audit log',
            'encrypt', 'lampiran',
            'VPN', 'ip-api',
            'middleware', 'ZeroTrustVerification',
            'AES-256', 'SHA-256', 'bcrypt']
found = {}
for i, para in enumerate(doc.paragraphs):
    text = para.text
    for kw in keywords:
        if kw.lower() in text.lower():
            found[kw] = found.get(kw, 0) + 1

print('=== Keywords found in laporan ===')
for f, count in sorted(found.items()):
    print(f'  OK {f} ({count}x)')
    
not_found = [k for k in keywords if k not in found]
if not_found:
    print()
    print('=== Keywords NOT found ===')
    for f in not_found:
        print(f'  MISSING {f}')
