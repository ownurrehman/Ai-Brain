> **Parent Report Hub:** [[system/reports/INDEX|📊 System Reports Archive]] · [[websites/rankray.com/index|rankray.com Hub]] · [[INDEX|🧠 Ai Brain]]

# Rank Ray Persistent Admin Flow

Use this flow for all Rank Ray WordPress admin work.

## Rules
- Keep one persistent browser profile for Rank Ray.
- Do not spawn fresh contexts for every step.
- Do not restart gateway or Mission Control during edit sessions.
- Stay in the same browser run from login to CPT list to edit page to save.
- If wp-admin root works but CPT pages bounce to login, inspect cookie domain/path and CPT capability settings.

## Observed behavior
- Login can succeed.
- wp-admin root can load.
- `edit.php?post_type=location_page` may still redirect to wp-login.
- The redirect suggests either a session scope mismatch or a capability / plugin gate on the CPT route.

## Checks to run
1. Confirm the browser profile is the same one used during login.
2. Confirm cookies exist for `rankray.com` and `www.rankray.com`.
3. Confirm the login used the same domain as the CPT route.
4. Confirm the user can access the `location_page` post type.
5. Confirm no security plugin or redirect rule is forcing reauth on admin subpaths.

## Working admin sequence
1. Open `https://www.rankray.com/wp-login.php`
2. Log in once.
3. Without changing browser profile, open `https://www.rankray.com/wp-admin/`
4. Go to Location Pages.
5. Open the target location page.
6. Fill ACF fields.
7. Save.

## Failure diagnosis
- If login works but CPT list bounces, the issue is not basic authentication.
- Most likely causes:
  - cookie path or domain mismatch
  - capability restrictions on the CPT
  - redirect/security plugin on specific admin routes
  - mixed `rankray.com` vs `www.rankray.com` session scope

## Best next action
Keep the same browser profile and test the CPT route immediately after login before opening any other admin page.
