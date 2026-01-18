ARG IMAGE_VERSION=local

FROM evoting/composer:${IMAGE_VERSION} AS composer_base

# For the frontend, we want to get all the Laravel files,
# and run a production compile
FROM node:22

# We need to copy in the Laravel files to make everything is available to our frontend compilation
COPY --from=composer_base /opt/apps/evoting /opt/apps/evoting

WORKDIR /opt/apps/evoting

# We want to install all the NPM packages,
# and compile the MIX bundle for production
RUN npm install && \
    npm run dev
