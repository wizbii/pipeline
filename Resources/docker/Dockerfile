FROM wizbii/php:7.1-systemd

COPY . ${BASE_PATH}

RUN chown -R www-data ${BASE_PATH}/var

ENV SYMFONY_ENV prod

RUN composer install --no-dev --classmap-authoritative --no-scripts --prefer-dist --no-progress --no-interaction

RUN ${BASE_PATH}/bin/generate-systemd-units.sh

ONBUILD LABEL org.label-schema.vendor="Wizbii" \
              org.label-schema.schema-version="1.0" \
              org.label-schema.build-date="${BUILD_DATE}" \
              org.label-schema.vcs-ref="$GIT_REFERENCE" \
              com.wizbii.type="pipeline"
