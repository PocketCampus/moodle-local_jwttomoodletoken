# Production Deployment

1. Copy into `local/jwttomoodletoken` directory. Install it in the Moodle Web interface. Configure it, for example, by settings `userinfo_url` to `https://login.eduid.ch/idp/profile/oidc/userinfo` and `username_attribute` to `swissEduIDLinkedAffiliationUniqueID`.

2. Create a role with the `local/jwttomoodletoken:usews` capability.

3. Create a user and assign it this role in the system context.

4. In Moodle's administration, chose Web Services > Manage Tokens, and create a token for this user – if needed use IP address restriction.

# Requests

Try for instance this to request a mobile token for a given access token:

```
https://your.moodle/webservice/rest/server.php?wstoken=<YOUR_TOKEN>>&wsfunction=local_jwttomoodletoken_gettoken&accesstoken=
<ACCESS_TOKEN>&moodlewsrestformat=json
```
