# Design: Baseline Blackbox Testing (Before Zero Trust)

## Overview
The goal is to perform a comprehensive "blackbox" test of the OS-Tiket application before the implementation of Zero Trust security. This establishes a baseline for comparison. The testing will be automated using Laravel Feature Tests to simulate HTTP requests and user interactions, allowing for fast, reproducible, and consistent test execution.

## 1. Test Suite Structure
The test cases outlined in `BLACKBOX_TEST_PLAN.md` will be implemented as Laravel Feature Tests. 
They will be organized inside a new namespace `Tests\Feature\BaselineBlackbox` to isolate them from other tests.

Files to be created:
- `tests/Feature/BaselineBlackbox/AuthenticationBaselineTest.php`
- `tests/Feature/BaselineBlackbox/GuestPortalBaselineTest.php`
- `tests/Feature/BaselineBlackbox/PortalUserBaselineTest.php`
- `tests/Feature/BaselineBlackbox/AgentPanelBaselineTest.php`
- `tests/Feature/BaselineBlackbox/AdminManagementBaselineTest.php`

## 2. Test Environment
- **Zero Trust Disabled**: The tests will forcefully override the `.env` settings by setting `ZERO_TRUST_ENABLED=false` within the test configuration (`phpunit.xml` or `TestCase.php`).
- **Database**: The testing environment will use an in-memory `sqlite` database if compatible, or a dedicated `mysql` testing database (`os_tiket_testing`) to prevent data corruption in the development database. It will run migrations and seeders before tests.

## 3. Execution & Verification Strategy
For each test case:
1. **Act**: The test will simulate an HTTP request (e.g., `post('/login', [...])`).
2. **Assert Response**: The test will verify the HTTP status code (200, 302, 403, etc.) and inspect session states or JSON responses.
3. **Assert Database**: The test will use `assertDatabaseHas` or `assertDatabaseMissing` to verify that the side-effects occurred in the database correctly (e.g., ticket created, status updated).
4. **Assert UI/HTML**: Where applicable, the test will verify that specific text (e.g., error messages, ticket subjects) is rendered in the returned HTML view.

## 4. Reporting
Once the test suite runs successfully (`php artisan test --filter BaselineBlackbox`), the passing output will serve as the verifiable proof that the baseline system functions correctly. The results will then be summarized and added to the official `Laporan_TA_ZeroTrust.docx` document.
