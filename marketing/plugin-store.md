Patrol handles two pieces of site infrastructure that should be explicit and dependable: maintenance access and canonical HTTPS routing. Put the public site offline deliberately, or keep requests on the correct secure domain.

Send public visitors to a project-owned offline page or response while approved users continue to work. Access rules can account for signed-in users, IP addresses, and the routes that must remain reachable.

## Features

- **Maintenance mode:** Take the public site offline without disabling Craft itself.
- **Custom offline response:** Present a branded page or another response owned by the project.
- **Controlled bypass:** Let approved users or addresses continue through during maintenance.
- **Access-token links:** Give an approved visitor a link that adds their current IP address to the allowlist.
- **HTTPS enforcement:** Redirect selected site requests onto a secure scheme.
- **Primary domain:** Choose whether the canonical host uses a bare or www-prefixed domain.
- **Redirect control:** Use the status code and route scope appropriate for the deployment.
- **HTTPS and domains:** Force HTTPS, choose the primary domain, limit where secure routing applies, and set the redirect status that fits the deployment. The rules remain application-aware rather than depending on one web-server configuration.
