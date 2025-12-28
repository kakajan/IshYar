# Authentication & Authorization Specification

## Purpose

The Authentication & Authorization module provides secure identity management, session handling, and role-based access control for all IshYar users across web and mobile interfaces.

## Requirements

### Requirement: User Authentication
The system SHALL provide secure multi-factor authentication with JWT-based session management.

#### Scenario: Standard login with credentials
- **WHEN** a user submits valid email and password
- **THEN** validate credentials against hashed storage
- **AND** issue a JWT access token (15-minute expiry)
- **AND** issue a refresh token (7-day expiry, HTTP-only cookie)
- **AND** log the authentication event with IP and user agent

#### Scenario: Invalid credentials
- **WHEN** a user submits invalid credentials
- **THEN** return a generic "Invalid credentials" error
- **AND** increment failed login counter
- **AND** enforce rate limiting (5 attempts per 15 minutes)

#### Scenario: Two-Factor Authentication (optional)
- **WHEN** 2FA is enabled for the user account
- **THEN** require OTP verification after password validation
- **AND** support TOTP (Google Authenticator compatible)
- **AND** provide backup recovery codes

### Requirement: Token Refresh
The system SHALL support seamless token refresh without requiring re-authentication.

#### Scenario: Refreshing expired access token
- **WHEN** the access token is expired but refresh token is valid
- **THEN** issue a new access token
- **AND** optionally rotate the refresh token
- **AND** maintain user session continuity

#### Scenario: Refresh token expired
- **WHEN** both access and refresh tokens are expired
- **THEN** require full re-authentication
- **AND** clear all session data

### Requirement: Role-Based Access Control
The system SHALL enforce granular permissions based on user roles and organizational hierarchy.

#### Scenario: Checking permission for action
- **WHEN** a user attempts an action requiring authorization
- **THEN** verify user has required permission
- **AND** check organizational scope (department/company-wide)
- **AND** log access attempt for audit trail

#### Scenario: Permission inheritance
- **WHEN** a user has a role with inherited permissions
- **THEN** aggregate all permissions from role hierarchy
- **AND** apply most permissive rule for overlapping permissions

### Requirement: Single Sign-On (SSO)
The system SHALL support enterprise SSO via OAuth2/OIDC providers.

#### Scenario: Google Workspace SSO
- **WHEN** organization has Google Workspace SSO configured
- **THEN** redirect to Google OAuth consent screen
- **AND** create or link user account on successful authentication
- **AND** sync organizational email domain restrictions

#### Scenario: Microsoft Azure AD SSO
- **WHEN** organization has Azure AD SSO configured
- **THEN** support OIDC authentication flow
- **AND** map Azure AD groups to IshYar roles

### Requirement: Session Management
The system SHALL provide comprehensive session control and security.

#### Scenario: Viewing active sessions
- **WHEN** a user requests their active sessions
- **THEN** display all devices/locations with active tokens
- **AND** show last activity timestamp for each session

#### Scenario: Revoking sessions
- **WHEN** a user revokes a session
- **THEN** invalidate the associated refresh token
- **AND** force logout on the target device
- **AND** notify the revoked session via WebSocket

### Requirement: Password Policy
The system SHALL enforce configurable password policies per organization.

#### Scenario: Password creation
- **WHEN** a user creates or updates their password
- **THEN** enforce minimum length (12 characters default)
- **AND** require complexity rules (uppercase, lowercase, number, special)
- **AND** check against common password databases
- **AND** prevent reuse of last 5 passwords

## API Endpoints

### POST /api/v1/auth/login
Authenticate user with credentials.

**Request:**
```json
{
  "email": "user@company.com",
  "password": "securePassword123!",
  "remember_me": true
}
```

**Response (200):**
```json
{
  "data": {
    "type": "auth_token",
    "attributes": {
      "access_token": "eyJhbGciOiJIUzI1NiIs...",
      "token_type": "Bearer",
      "expires_in": 900,
      "requires_2fa": false
    },
    "relationships": {
      "user": {
        "data": { "type": "users", "id": "uuid" }
      }
    }
  }
}
```

### POST /api/v1/auth/refresh
Refresh access token using refresh token from HTTP-only cookie.

### POST /api/v1/auth/logout
Invalidate current session and clear tokens.

### POST /api/v1/auth/2fa/verify
Verify OTP for two-factor authentication.

### GET /api/v1/auth/sessions
List all active sessions for current user.

### DELETE /api/v1/auth/sessions/{id}
Revoke specific session.

### POST /api/v1/auth/password/forgot
Initiate password reset flow.

### POST /api/v1/auth/password/reset
Complete password reset with token.

### GET /api/v1/auth/sso/{provider}
Initiate SSO authentication with specified provider.

### POST /api/v1/auth/sso/{provider}/callback
Handle SSO provider callback.
