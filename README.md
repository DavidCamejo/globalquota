# GlobalQuota - Nextcloud App v1.2.0

Define and monitor a global storage quota, compatible with S3 and external storage.

**NEW v1.1.0**: Blocks uploads when global quota is exceeded.  
**NEW v1.2.0**: Adaptive visualization with ServerInfo integration or standalone Chart.js donut.

---

## Features

- ✅ Global quota definition and monitoring
- ✅ Compatible with S3 and external storage backends
- ✅ Upload blocking when quota exceeded (PSR-14 events)
- ✅ OCC commands for management
- ✅ REST API for external integration
- ✅ Server Info dashboard integration
- ✅ **NEW (v1.2.0)** Adaptive visualization:
  - Integrates seamlessly into ServerInfo if supported
  - Shows its own donut chart in Admin Settings otherwise
- ✅ **Responsive donut chart** with used / free / total stats

---

## Configuration

Add to `config/config.php`:

```php
'globalquota' => [
    'enabled' => true,
    'quota_bytes' => 500 * 1024 * 1024 * 1024, // 500 GB
],
```

---

## OCC Commands

```bash
# Set quota (in bytes)
occ globalquota:set 2147483648   # 2 GB

# Check status
occ globalquota:status

# Force recalculation
occ globalquota:recalc
```

---

## API Endpoints

```bash
# Get current status (new endpoint for admin chart)
curl -H "Authorization: Bearer TOKEN" \
     https://instance.com/apps/globalquota/status

# Legacy API v1 endpoint
curl -H "Authorization: Bearer TOKEN" \
     https://instance.com/apps/globalquota/api/v1/status

# Update quota
curl -X PUT \
     -H "Authorization: Bearer TOKEN" \
     -H "Content-Type: application/json" \
     -d '{"quota_bytes": 10737418240}' \
     https://instance.com/apps/globalquota/api/v1/quota
```

---

## Upload Blocking

When the global quota is exceeded, the app will:
- Block new file uploads
- Block file updates that increase size
- Return HTTP 507 "Insufficient Storage" error
- Work with all clients (web, desktop, mobile, WebDAV)

---

## Visualization Modes (v1.2.0)

### 🔗 With ServerInfo
If ServerInfo supports events (`LoadAdditionalDataEvent`), GlobalQuota **replaces the Disk chart** with GlobalQuota data.

### 📊 Standalone
If ServerInfo does not support events, GlobalQuota shows its **own donut chart** in the Admin Settings section.

Both modes display:
- Usage percentage
- Used / Free / Total values
- Auto-refresh capability (via API)

---

## Requirements

- Nextcloud 25+
- Admin privileges for configuration
- Optional: ServerInfo app (for dashboard integration)

---

## Changelog

- **v1.2.0**
  - Adaptive visualization (ServerInfo integration or standalone Chart.js donut)
  - New `/apps/globalquota/status` endpoint
  - Improved admin settings panel

- **v1.1.0**
  - Upload blocking functionality with PSR-14 events
  - OCC commands and REST API integration

- **v1.0.0**
  - Initial release with basic quota management and S3 compatibility

---

**Author:** David Camejo  
**License:** AGPL-3.0
```
