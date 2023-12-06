ARG TISSUE_FOUNDATION_IMAGE_NAME

FROM ${TISSUE_FOUNDATION_IMAGE_NAME} as foundation

FROM nginx:alpine

COPY ./docker/production/config/default.conf.template /etc/nginx/templates/

COPY --from=foundation /app/public /app/public
