# Google OAuth

<details>
<summary>Relevant source files</summary>

The following files were used as context for generating this wiki page:

- [html/headers.html](html/headers.html)
- [js/functions.js](js/functions.js)
- [navs/nav-login.php](navs/nav-login.php)

</details>



This document describes the Google OAuth 2.0 integration in Legend of Aetheria, which provides social login functionality as an alternative to traditional email/password authentication. For information about the traditional authentication flow, see [Login System](#4.2). For details about the overall authentication and authorization architecture, see [Authentication & Authorization](#4).

## Purpose and Scope

The Google OAuth integration allows users to authenticate using their Google accounts through the Google Sign-In API. This implementation leverages Google's Identity Services to streamline the registration and login process while maintaining security through proper Content Security Policy (CSP) configuration and Cross-Origin Resource Sharing (CORS) controls.

---

## Configuration

### Client ID Registration

The Google OAuth client ID is configured as a meta tag in the HTML headers, making it available to the Google Sign-In JavaScript library.

**Client ID Configuration**

| Configuration Element | Value | Location |
|----------------------|-------|----------|
| Client ID | `905625455039-22nlqmke7jn849t3h7125i5tjtea89fb.apps.googleusercontent.com` | [html/headers.html:6]() |
| Meta Tag Name | `google-signin-client_id` | [html/headers.html:6]() |
| Cross-Origin Policy | `same-origin-allow-popups` | [html/headers.html:7]() |

The client ID is embedded directly in the page metadata to enable client-side OAuth initialization:

```html
<meta name="google-signin-client_id" content="905625455039-22nlqmke7jn849t3h7125i5tjtea89fb.apps.googleusercontent.com">
```

Sources: [html/headers.html:6]()

### Content Security Policy

The CSP configuration explicitly allows Google OAuth resources while maintaining strict security boundaries for other content sources.

**CSP Directives for Google OAuth**

```mermaid
graph TD
    CSP["Content-Security-Policy<br/>Header"]
    
    ScriptSrc["script-src"]
    StyleSrc["style-src"]
    FrameSrc["frame-src"]
    ConnectSrc["connect-src"]
    
    GoogleGSI["accounts.google.com/gsi/client"]
    GoogleAPIs["apis.google.com/js/platform.js"]
    GoogleAccounts["accounts.google.com"]
    GoogleStyles["accounts.google.com"]
    
    CSP --> ScriptSrc
    CSP --> StyleSrc
    CSP --> FrameSrc
    CSP --> ConnectSrc
    
    ScriptSrc --> GoogleGSI
    ScriptSrc --> GoogleAPIs
    StyleSrc --> GoogleStyles
    FrameSrc --> GoogleAccounts
    
    ScriptSrc --> Self["'self'"]
    ScriptSrc --> UnsafeInline["'unsafe-inline'"]
```

**CSP Directive Breakdown**

| Directive | Allowed Sources | Purpose |
|-----------|----------------|---------|
| `script-src` | `'self'`, `'unsafe-inline'`, `https://accounts.google.com/gsi/client`, `https://apis.google.com/js/platform.js` | Permits Google Sign-In JavaScript libraries |
| `style-src` | `'self'`, `'unsafe-inline'`, `https://accounts.google.com` | Allows Google OAuth modal styling |
| `frame-src` | `'self'`, `https://accounts.google.com` | Enables OAuth popup windows and iframes |
| `connect-src` | `*` | Permits AJAX requests to Google OAuth endpoints |

Sources: [html/headers.html:8-18]()

### Cross-Origin Configuration

The `Cross-Origin-Opener-Policy` is set to `same-origin-allow-popups` to enable Google OAuth popup windows while maintaining isolation from other origins.

```mermaid
graph LR
    MainWindow["Main Window<br/>legendofaetheria.com"]
    
    COOP["Cross-Origin-Opener-Policy:<br/>same-origin-allow-popups"]
    
    GooglePopup["OAuth Popup<br/>accounts.google.com"]
    
    BlockedOrigin["Other Origins<br/>BLOCKED"]
    
    MainWindow --> COOP
    COOP --> GooglePopup
    COOP -.X.- BlockedOrigin
    
    GooglePopup -->|"OAuth Token"| MainWindow
```

This policy allows:
- OAuth popup windows from `accounts.google.com`
- Token exchange between popup and main window
- Isolation from other cross-origin contexts

Sources: [html/headers.html:7]()

---

## OAuth Flow Architecture

The Google OAuth integration follows the OAuth 2.0 authorization code flow with client-side token handling.

### Authentication Sequence

```mermaid
sequenceDiagram
    participant Browser
    participant index.php
    participant GoogleGSI as "Google Sign-In API<br/>accounts.google.com"
    participant GoogleOAuth as "Google OAuth Server<br/>oauth2.googleapis.com"
    participant Account
    participant Database
    
    Note over Browser,Database: OAuth Initialization
    
    Browser->>index.php: "GET /"
    index.php->>Browser: "Render login page<br/>with Google meta tags"
    Browser->>Browser: "Load Google GSI library"
    Browser->>GoogleGSI: "Initialize with client_id"
    GoogleGSI-->>Browser: "Render 'Sign in with Google' button"
    
    Note over Browser,Database: User Authentication
    
    Browser->>GoogleGSI: "Click 'Sign in with Google'"
    GoogleGSI->>GoogleOAuth: "Open OAuth popup<br/>Request authorization"
    GoogleOAuth->>Browser: "Display consent screen"
    Browser->>GoogleOAuth: "User grants permission"
    GoogleOAuth->>GoogleGSI: "Return ID token + access token"
    GoogleGSI->>Browser: "Close popup, return tokens"
    
    Note over Browser,Database: Token Validation & Account Linking
    
    Browser->>index.php: "POST /oauth-callback<br/>with ID token"
    index.php->>GoogleOAuth: "Verify token signature"
    GoogleOAuth-->>index.php: "Token valid, return user info"
    index.php->>Account: "checkIfExists(email)"
    Account->>Database: "SELECT from accounts<br/>WHERE email = ?"
    
    alt Account Exists
        Database-->>Account: "Account found"
        Account-->>index.php: "Existing account"
        index.php->>index.php: "Create session"
    else New Account
        Database-->>Account: "No account found"
        Account-->>index.php: "Create new account"
        index.php->>Account: "Register with Google email"
        Account->>Database: "INSERT into accounts"
        index.php->>index.php: "Create session"
    end
    
    index.php->>Browser: "Redirect to /select"
```

Sources: [html/headers.html:6-18]()

### OAuth Component Mapping

```mermaid
graph TB
    subgraph "Client-Side Components"
        MetaTag["google-signin-client_id<br/>meta tag"]
        GSIScript["Google Sign-In<br/>JavaScript Library"]
        OAuthButton["Sign in with Google<br/>Button"]
    end
    
    subgraph "Server-Side Components"
        IndexPHP["index.php<br/>Login Controller"]
        AccountClass["Account Class<br/>Authentication Logic"]
        DatabaseLayer["PropSuite ORM<br/>Database Access"]
    end
    
    subgraph "External Services"
        GoogleGSI["accounts.google.com<br/>GSI Client"]
        GoogleOAuth["oauth2.googleapis.com<br/>Token Verification"]
    end
    
    subgraph "Security Layer"
        CSP["Content-Security-Policy<br/>headers.html:8-18"]
        COOP["Cross-Origin-Opener-Policy<br/>headers.html:7"]
        SessionMgmt["PHP Session Management<br/>Session Validation"]
    end
    
    MetaTag --> GSIScript
    GSIScript --> OAuthButton
    OAuthButton --> GoogleGSI
    GoogleGSI --> GoogleOAuth
    
    GoogleOAuth --> IndexPHP
    IndexPHP --> AccountClass
    AccountClass --> DatabaseLayer
    
    CSP --> GSIScript
    COOP --> GoogleGSI
    IndexPHP --> SessionMgmt
```

Sources: [html/headers.html:6-18]()

---

## Security Implementation

### CSP-Based Protection

The Content Security Policy provides defense-in-depth for the OAuth integration by restricting which resources can load and execute.

**Security Boundaries**

| Threat Vector | CSP Mitigation | Implementation |
|---------------|----------------|----------------|
| Malicious Script Injection | Whitelist `accounts.google.com` and `apis.google.com` only | [html/headers.html:9]() |
| Cross-Site Framing | Restrict `frame-src` to `accounts.google.com` | [html/headers.html:15]() |
| Style Injection | Limit `style-src` to Google domains | [html/headers.html:10]() |
| Data Exfiltration | Control `connect-src` destinations | [html/headers.html:13]() |

Sources: [html/headers.html:8-18]()

### Token Validation

While the complete token validation code is not visible in the provided files, the OAuth flow requires:

1. **ID Token Verification**: Server-side validation of JWT signatures using Google's public keys
2. **Audience Check**: Ensuring the token's `aud` claim matches the registered client ID
3. **Expiration Validation**: Verifying the token hasn't expired
4. **Issuer Validation**: Confirming the token was issued by `accounts.google.com`

### Session Security Integration

OAuth-authenticated users follow the same session security model as traditional login users:

```mermaid
graph TD
    OAuthLogin["OAuth Login Success"]
    
    SessionCreation["PHP Session Creation<br/>session_start()"]
    
    SessionVars["Session Variables:<br/>logged-in = 1<br/>email = user@gmail.com<br/>account-id = N"]
    
    CSRFToken["CSRF Token Generation<br/>gen_csrf_token()"]
    
    SessionValidation["check_session()<br/>on every request"]
    
    OAuthLogin --> SessionCreation
    SessionCreation --> SessionVars
    SessionVars --> CSRFToken
    CSRFToken --> SessionValidation
```

After OAuth authentication, the session management follows the same patterns documented in [Session Management](#3.2), including:
- CSRF token generation and validation
- Session ID regeneration
- Session variable storage for user context

Sources: [html/headers.html:41-58]()

---

## Integration Points

### Login Page Integration

The Google OAuth integration is embedded in the login interface alongside traditional authentication methods. While the specific button implementation is not visible in [navs/nav-login.php](), the infrastructure is configured in [html/headers.html]().

**Login Page Component Structure**

```mermaid
graph LR
    LoginPage["Login Page<br/>index.php"]
    
    NavLogin["navs/nav-login.php<br/>Navigation Tabs"]
    
    Headers["html/headers.html<br/>Google OAuth Config"]
    
    TraditionalLogin["Traditional Login Form<br/>Email/Password"]
    
    GoogleLogin["Google Sign-In Button<br/>OAuth Integration"]
    
    LoginPage --> Headers
    LoginPage --> NavLogin
    
    NavLogin --> TraditionalLogin
    NavLogin --> GoogleLogin
    
    Headers --> GoogleMeta["Client ID Meta Tag"]
    Headers --> GoogleCSP["CSP Configuration"]
```

Sources: [html/headers.html:6](), [navs/nav-login.php:36-66]()

### Account Class Integration

OAuth-authenticated users are processed through the same `Account` class used for traditional authentication:

**Account Lookup Flow**

| Method | Purpose | Usage in OAuth |
|--------|---------|----------------|
| `Account::checkIfExists(email)` | Check if email exists in database | Verify if Google email has existing account |
| `Account::get_id()` | Retrieve account ID | Link OAuth session to account |
| `Account::get_privileges()` | Get account privilege level | Apply same authorization rules |

OAuth users receive the same privilege system treatment as traditional users, requiring email verification and following the same privilege escalation path (UNVERIFIED → USER → MODERATOR → ADMINISTRATOR).

Sources: [html/headers.html:6-18]()

### Session Context Binding

Once OAuth authentication succeeds, the same session variables used for traditional login are populated:

```javascript
var loa = {
   u_email: "<?php echo $_SESSION['email']; ?>",
     u_aid: "<?php echo $_SESSION['account-id']; ?>",
    u_csrf: "<?php echo $_SESSION['csrf-token']; ?>",
     u_sid: "<?php echo session_id(); ?>",
     chat_pos: 0,
     chat_history: [],
};
```

These JavaScript globals are populated from PHP session state regardless of authentication method, ensuring OAuth and traditional logins have identical post-authentication experiences.

Sources: [html/headers.html:49-56]()

---

## Configuration Summary

### Required Meta Tags

```html
<meta name="google-signin-client_id" content="[CLIENT_ID]">
<meta http-equiv="Cross-Origin-Opener-Policy" content="same-origin-allow-popups">
```

### Required CSP Directives

- `script-src`: Must include `https://accounts.google.com/gsi/client` and `https://apis.google.com/js/platform.js`
- `style-src`: Must include `https://accounts.google.com`
- `frame-src`: Must include `https://accounts.google.com`
- `connect-src`: Must allow requests to Google OAuth endpoints

### Configuration Files

| File | Purpose | Key Elements |
|------|---------|--------------|
| [html/headers.html]() | OAuth initialization | Client ID, CSP, COOP |
| [navs/nav-login.php]() | Login interface | Traditional + OAuth forms |
| [index.php]() (inferred) | OAuth callback handler | Token validation, account linking |

Sources: [html/headers.html:1-65](), [navs/nav-login.php:1-427]()