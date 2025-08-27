# GlobalQuota - Nextcloud App

Define and monitor a global storage quota, compatible with S3 and external storage.

## Configuration

Add to `config.php`:

```php
'globalquota' => [
    'enabled' => true,
    'quota_bytes' => 500 * 1024 * 1024 * 1024, // 500 GB
],
```

## OCC Commands

```bash
occ globalquota:set 2147483648   # 2 GB
occ globalquota:status
occ globalquota:recalc
```

## API

- `GET /apps/globalquota/api/v1/status`
- `PUT /apps/globalquota/api/v1/quota { "quota_bytes": 10737418240 }`
