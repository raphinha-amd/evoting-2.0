ARG IMAGE_VERSION=production

FROM evoting/frontend:${IMAGE_VERSION} AS frontend

# We need an nginx container which can pass requests to our FPM container,
# as well as serve any static content.
FROM nginx:1.20-alpine

WORKDIR /opt/apps/evoting

# We need to add our NGINX template to the container for startup,
# and configuration.
COPY ops/docker/production/nginx/nginx.conf.template /etc/nginx/templates/default.conf.template

# Copy in ONLY the public directory of our project.
# This is where all the static assets will live, which nginx will serve for us.
COPY --from=frontend /opt/apps/evoting/public /opt/apps/evoting/public
