FROM wizbii/php:7.1-systemd

ARG GIT_REFERENCE
ENV GIT_REFERENCE ${GIT_REFERENCE:-HEAD}

ARG BASE_PATH
ENV BASE_PATH ${BASE_PATH:-/code}

COPY . ${BASE_PATH}

RUN chown -R www-data ${BASE_PATH}/var

ENV SYMFONY_ENV prod

RUN ${BASE_PATH}/bin/generate-systemd-units.sh

