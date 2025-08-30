# GlobalQuota - Nextcloud App v1.2.1

Define and monitor a global storage quota, compatible with S3 and external storage.

**NEW v1.2.1**: Enhanced UI reliability with fallback text display and improved debugging.  
**NEW v1.2.0**: Adaptive visualization with ServerInfo integration or standalone Chart.js donut.  
**NEW v1.1.0**: Blocks uploads when global quota is exceeded.

---

## Features

* ✅ **Global quota definition and monitoring**
* ✅ **Compatible with S3 and external storage backends**
* ✅ **Upload blocking when quota exceeded** (PSR-14 events)
* ✅ **OCC commands** for management
* ✅ **REST API** for external integration
* ✅ **Server Info dashboard integration**
* ✅ **Adaptive visualization (v1.2.0)**:
  + Integrates seamlessly into ServerInfo if supported
  + Shows its own donut chart in Admin Settings otherwise
* ✅ **Enhanced reliability (v1.2.1)**:
  + Always displays quota values in text format
  + Robust error handling and debugging logs
  + Chart.js fallback mechanisms

---

### Admin Settings Panel
The app displays quota information with both visual chart and text values:
- **Used storage**: Shows current usage
- **Free storage**: Available space remaining  
- **Total storage**: Global quota limit
- **Usage percentage**: Visual indicator with color coding

---

## Installation

1. **Download** or clone this repository
2. **Copy** the `globalquota` folder to your Nextcloud `apps/` directory
3. **Enable** the app in Nextcloud Admin Settings → Apps
4. **Configure** the global quota in Admin Settings → Additional Settings → Global Quota

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

* ❌ **Block new file uploads**
* ❌ **Block file updates that increase size**
* 🚫 **Return HTTP 507 "Insufficient Storage" error**
* 📱 **Work with all clients** (web, desktop, mobile, WebDAV)

---

## Visualization Modes

### 🔗 **With ServerInfo Integration**
If ServerInfo supports events (`LoadAdditionalDataEvent`), GlobalQuota **replaces the Disk chart** with GlobalQuota data.

### 📊 **Standalone Mode**
If ServerInfo does not support events, GlobalQuota shows its **own donut chart** in the Admin Settings section.

### 📝 **Text Fallback (v1.2.1)**
Even if the chart fails to load, quota values are **always displayed in text format** for reliability.

**Both modes display:**
- Usage percentage with color coding
- Used / Free / Total values (formatted)
- Auto-refresh capability via "Refresh" button
- Detailed console logging for debugging

---

## Troubleshooting

### Chart not displaying?
1. Check browser console for errors (`F12` → Console)
2. Look for `GlobalQuota:` log messages
3. Verify Chart.js is loading (CDN or local)
4. Text values should still display regardless

### API errors?
1. Verify app is enabled and configured
2. Check Nextcloud logs for backend errors
3. Test endpoints directly with curl

---

## Requirements

* **Nextcloud 25+**
* **Admin privileges** for configuration
* **Optional**: ServerInfo app (for dashboard integration)
* **Browser**: Modern browser with JavaScript enabled

---

## Changelog

* **v1.2.1** *(Latest)*
  + Enhanced UI reliability with always-visible text values
  + Improved error handling and debugging logs
  + Chart.js fallback mechanisms
  + Better console logging for troubleshooting

* **v1.2.0**
  + Adaptive visualization (ServerInfo integration or standalone Chart.js donut)
  + New `/apps/globalquota/status` endpoint
  + Improved admin settings panel

* **v1.1.0**
  + Upload blocking functionality with PSR-14 events
  + OCC commands and REST API integration

* **v1.0.0**
  + Initial release with basic quota management and S3 compatibility

---

## Contributing

1. **Fork** this repository
2. **Create** a feature branch
3. **Make** your changes
4. **Test** thoroughly
5. **Submit** a pull request

### Reporting Issues
Please report bugs and feature requests on [GitHub Issues](https://github.com/DavidCamejo/globalquota/issues).

---

**Author:** David Camejo  
**License:** AGPL-3.0  
**Repository:** https://github.com/DavidCamejo/globalquota
