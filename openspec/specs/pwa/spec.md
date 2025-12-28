# Progressive Web App (PWA) Specification

## Purpose

The PWA module defines the cross-platform installable application behavior, service worker strategies, offline capabilities, and native-like features for IshYar.

## Requirements

### Requirement: PWA Installation
The system SHALL be fully installable as a PWA across all platforms.

#### Scenario: Install prompt on desktop
- **WHEN** user visits IshYar on desktop browser
- **THEN** detect PWA install capability
- **AND** show custom install banner after engagement
- **AND** provide install button in settings
- **AND** handle successful installation feedback

#### Scenario: Install on mobile
- **WHEN** user visits on iOS/Android browser
- **THEN** show "Add to Home Screen" prompt
- **AND** provide platform-specific instructions
- **AND** display as standalone app when launched
- **AND** support both Safari and Chrome

#### Scenario: App icons and splash screens
- **WHEN** PWA is installed
- **THEN** use high-resolution app icons (all sizes)
- **AND** display custom splash screen on launch
- **AND** match brand colors in status bar
- **AND** use maskable icons for Android adaptive icons

### Requirement: Service Worker Strategy
The system SHALL implement efficient service worker caching strategies.

#### Scenario: App shell caching
- **WHEN** app loads
- **THEN** cache app shell (HTML, CSS, JS) immediately
- **AND** use cache-first strategy for static assets
- **AND** update cache in background on new version
- **AND** prompt user to refresh on major updates

#### Scenario: API response caching
- **WHEN** API requests are made
- **THEN** use network-first with cache fallback
- **AND** cache GET requests selectively
- **AND** implement stale-while-revalidate for lists
- **AND** never cache sensitive endpoints

#### Scenario: Offline detection
- **WHEN** network status changes
- **THEN** detect online/offline state
- **AND** show offline indicator in UI
- **AND** switch to offline mode gracefully
- **AND** queue write operations for sync

### Requirement: Background Sync
The system SHALL sync data when connectivity is restored.

#### Scenario: Queueing offline actions
- **WHEN** user performs action while offline
- **THEN** store action in IndexedDB queue
- **AND** show pending sync indicator
- **AND** prevent duplicate submissions
- **AND** maintain action ordering

#### Scenario: Background sync trigger
- **WHEN** connectivity is restored
- **THEN** trigger background sync event
- **AND** process queued actions sequentially
- **AND** handle conflicts appropriately
- **AND** notify user of sync completion

#### Scenario: Conflict resolution
- **WHEN** synced data conflicts with server
- **THEN** use last-write-wins for simple fields
- **AND** present manual resolution for complex conflicts
- **AND** preserve both versions when uncertain
- **AND** log conflict for analysis

### Requirement: Push Notifications (PWA)
The system SHALL support web push notifications.

#### Scenario: Permission request
- **WHEN** user should receive push notifications
- **THEN** request permission at appropriate time
- **AND** explain value proposition first
- **AND** respect denial gracefully
- **AND** provide settings to re-enable

#### Scenario: Displaying push notification
- **WHEN** push notification is received
- **THEN** show native OS notification
- **AND** include icon, badge, and image if available
- **AND** support action buttons
- **AND** handle click to open relevant view

#### Scenario: Silent notifications
- **WHEN** silent push is received
- **THEN** trigger background sync
- **AND** or update cached data
- **AND** without showing visible notification
- **AND** respect battery optimization

### Requirement: Local Data Storage
The system SHALL persist data locally for performance and offline use.

#### Scenario: IndexedDB usage
- **WHEN** data needs local persistence
- **THEN** store in IndexedDB (not localStorage)
- **AND** implement proper schema versioning
- **AND** handle storage quota limits
- **AND** encrypt sensitive data

#### Scenario: Cache management
- **WHEN** cache grows large
- **THEN** implement cache eviction policies
- **AND** prioritize frequently accessed data
- **AND** respect storage quota limits
- **AND** provide clear cache option

### Requirement: Native Feature Access
The system SHALL leverage native device features where available.

#### Scenario: Share API
- **WHEN** user shares content
- **THEN** use native share sheet (Web Share API)
- **AND** fall back to copy link on unsupported
- **AND** support sharing tasks, reports

#### Scenario: File handling
- **WHEN** user needs to open/save files
- **THEN** use File System Access API where available
- **AND** fall back to download/upload
- **AND** support drag-and-drop

#### Scenario: Notifications badge
- **WHEN** unread notifications exist
- **THEN** update app badge count (Badging API)
- **AND** clear badge when viewed
- **AND** fall back gracefully if unsupported

## Web App Manifest

```json
{
  "name": "IshYar WorkSuite",
  "short_name": "IshYar",
  "description": "Enterprise Task & Resource Management",
  "start_url": "/",
  "scope": "/",
  "display": "standalone",
  "orientation": "any",
  "theme_color": "#3B82F6",
  "background_color": "#FFFFFF",
  "categories": ["business", "productivity"],
  "icons": [
    {
      "src": "/icons/icon-72x72.png",
      "sizes": "72x72",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-96x96.png",
      "sizes": "96x96",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-128x128.png",
      "sizes": "128x128",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-144x144.png",
      "sizes": "144x144",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-152x152.png",
      "sizes": "152x152",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any"
    },
    {
      "src": "/icons/icon-384x384.png",
      "sizes": "384x384",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any"
    },
    {
      "src": "/icons/maskable-icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "maskable"
    }
  ],
  "screenshots": [
    {
      "src": "/screenshots/desktop-dashboard.png",
      "sizes": "1920x1080",
      "type": "image/png",
      "form_factor": "wide",
      "label": "Dashboard Overview"
    },
    {
      "src": "/screenshots/mobile-tasks.png",
      "sizes": "390x844",
      "type": "image/png",
      "form_factor": "narrow",
      "label": "Task List"
    }
  ],
  "shortcuts": [
    {
      "name": "New Task",
      "short_name": "New Task",
      "description": "Create a new task",
      "url": "/tasks/new",
      "icons": [{ "src": "/icons/shortcut-new-task.png", "sizes": "96x96" }]
    },
    {
      "name": "My Tasks",
      "short_name": "My Tasks",
      "description": "View your assigned tasks",
      "url": "/tasks/my",
      "icons": [{ "src": "/icons/shortcut-my-tasks.png", "sizes": "96x96" }]
    },
    {
      "name": "Approvals",
      "short_name": "Approvals",
      "description": "View pending approvals",
      "url": "/approvals",
      "icons": [{ "src": "/icons/shortcut-approvals.png", "sizes": "96x96" }]
    }
  ],
  "related_applications": [],
  "prefer_related_applications": false,
  "handle_links": "preferred",
  "launch_handler": {
    "client_mode": "navigate-existing"
  },
  "share_target": {
    "action": "/share-target",
    "method": "POST",
    "enctype": "multipart/form-data",
    "params": {
      "title": "title",
      "text": "text",
      "url": "url",
      "files": [
        {
          "name": "files",
          "accept": ["image/*", "application/pdf", ".doc", ".docx"]
        }
      ]
    }
  },
  "protocol_handlers": [
    {
      "protocol": "web+ishyar",
      "url": "/protocol?url=%s"
    }
  ]
}
```

## Service Worker Architecture

### Caching Strategy by Route
| Pattern | Strategy | Max Age |
|---------|----------|---------|
| `/` (App Shell) | Cache First | Until Update |
| `/assets/*` | Cache First | 1 Year |
| `/api/v1/users/me` | Network First | 5 min |
| `/api/v1/tasks*` | Stale While Revalidate | 1 min |
| `/api/v1/notifications` | Network Only | - |
| `/api/v1/analytics/*` | Network First | 30 sec |

### Offline Fallbacks
```typescript
// Routes that work offline
const OFFLINE_ROUTES = [
  '/',
  '/tasks',
  '/tasks/my',
  '/profile',
  '/settings'
];

// Offline page for uncached routes
const OFFLINE_PAGE = '/offline.html';
```

### Background Sync Tags
| Tag | Purpose | Retry Strategy |
|-----|---------|----------------|
| `task-create` | Create new task | Exponential backoff |
| `task-update` | Update task | Exponential backoff |
| `task-progress` | Update progress | Coalesce to latest |
| `comment-add` | Add comment | Exponential backoff |
| `time-entry` | Log time | Queue all |

## Data Schema

### OfflineAction
```typescript
interface OfflineAction {
  id: UUID;
  type: 'create' | 'update' | 'delete';
  entity: string;
  entity_id?: UUID;
  payload: Record<string, any>;
  timestamp: Date;
  retries: number;
  error?: string;
  status: 'pending' | 'syncing' | 'synced' | 'failed';
}
```

### CacheMetadata
```typescript
interface CacheMetadata {
  url: string;
  cached_at: Date;
  expires_at: Date;
  etag?: string;
  last_modified?: string;
}
```
