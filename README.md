# GlobalQuota - Nextcloud App v1.1.0

Define and monitor a global storage quota, compatible with S3 and external storage.
**NEW**: Blocks uploads when global quota is exceeded.

## Features

- ✅ Global quota definition and monitoring
- ✅ Compatible with S3 and external storage backends
- ✅ Upload blocking when quota exceeded (PSR-14 events)
- ✅ OCC commands for management
- ✅ REST API for external integration
- ✅ Server Info dashboard integration

## Configuration

Add to `config/config.php`:

```php
'globalquota' => [
    'enabled' => true,
    'quota_bytes' => 500 * 1024 * 1024 * 1024, // 500 GB
],
```

## OCC Commands

```bash
# Set quota (in bytes)
occ globalquota:set 2147483648   # 2 GB

# Check status
occ globalquota:status

# Force recalculation
occ globalquota:recalc
```

## API Endpoints

```bash
# Get current status
curl -H "Authorization: Bearer TOKEN" \
     https://instance.com/apps/globalquota/api/v1/status

# Update quota
curl -X PUT \
     -H "Authorization: Bearer TOKEN" \
     -H "Content-Type: application/json" \
     -d '{"quota_bytes": 10737418240}' \
     https://instance.com/apps/globalquota/api/v1/quota
```

## Upload Blocking

When the global quota is exceeded, the app will:
- Block new file uploads
- Block file updates that increase size
- Return HTTP 507 "Insufficient Storage" error
- Work with all clients (web, desktop, mobile, WebDAV)

## Requirements

- Nextcloud 25+
- Admin privileges for configuration
