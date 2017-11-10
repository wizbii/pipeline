FROM wizbii/php:7.1-systemd

ARG GIT_REFERENCE
ENV GIT_REFERENCE ${GIT_REFERENCE:-HEAD}

ARG BASE_PATH
ENV BASE_PATH ${BASE_PATH:-/code}

ARG BUILD_DATE

COPY . ${BASE_PATH}

RUN chown -R www-data ${BASE_PATH}/var

ENV SYMFONY_ENV prod

RUN ${BASE_PATH}/bin/generate-systemd-units.sh

LABEL org.label-schema.docker.schema-version="1.0" \
      org.label-schema.vendor="Wizbii" \
      org.label-schema.vcs-ref=$GIT_REFERENCE \
      org.label-schema.build-date=$BUILD_DATE \
      com.wizbii.type="pipeline"
